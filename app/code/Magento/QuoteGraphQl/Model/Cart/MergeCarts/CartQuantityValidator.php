<?php
/**
 * Copyright 2019 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\QuoteGraphQl\Model\Cart\MergeCarts;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Checkout\Model\Config;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartItemRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Model\Quote\Item;

class CartQuantityValidator implements CartQuantityValidatorInterface
{
    /**
     * @var array
     */
    private array $cumulativeQty = [];

    /**
     * CartQuantityValidator Constructor
     *
     * @param CartItemRepositoryInterface $cartItemRepository
     * @param StockRegistryInterface $stockRegistry
     * @param Config $config
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        private readonly CartItemRepositoryInterface $cartItemRepository,
        private readonly StockRegistryInterface $stockRegistry,
        private readonly Config $config,
        private readonly ProductRepositoryInterface $productRepository
    ) {
    }

    /**
     * Validate combined cart quantities to make sure they are within available stock
     *
     * @param CartInterface $customerCart
     * @param CartInterface $guestCart
     * @return bool
     */
    public function validateFinalCartQuantities(CartInterface $customerCart, CartInterface $guestCart): bool
    {
        $modified = false;
        $this->cumulativeQty = [];

        foreach ($guestCart->getAllVisibleItems() as $guestCartItem) {
            foreach ($customerCart->getAllVisibleItems() as $customerCartItem) {
                if (!$customerCartItem->compare($guestCartItem)) {
                    continue;
                }

                if ($this->config->getCartMergePreference() === Config::CART_PREFERENCE_CUSTOMER) {
                    $this->safeDeleteCartItem((int) $guestCart->getId(), (int) $guestCartItem->getItemId());
                    $modified = true;
                    continue;
                }

                $sku = $this->getSkuFromItem($customerCartItem);
                $product = $this->getProduct((int) $customerCartItem->getProduct()->getId());
                $isAvailable = $customerCartItem->getChildren()
                    ? $this->validateCompositeProductQty($product, $guestCartItem, $customerCartItem)
                    : $this->validateProductQty($product, $sku, $guestCartItem->getQty(), $customerCartItem->getQty());

                if ($this->config->getCartMergePreference() === Config::CART_PREFERENCE_GUEST) {
                    $this->safeDeleteCartItem((int) $customerCart->getId(), (int) $customerCartItem->getItemId());
                    $modified = true;
                }

                if (!$isAvailable) {
                    $this->safeDeleteCartItem((int) $guestCart->getId(), (int) $guestCartItem->getItemId());
                    $modified = true;
                }
            }
        }

        return $modified;
    }

    /**
     * Get SKU from Cart Item
     *
     * @param CartItemInterface $item
     * @return string
     */
    private function getSkuFromItem(CartItemInterface $item): string
    {
        return $item->getProduct()->getOptions()
            ? $this->getProduct((int) $item->getProduct()->getId())->getSku()
            : $item->getProduct()->getSku();
    }

    /**
     * Get current cart item quantity based on merge preference
     *
     * @param float $guestQty
     * @param float $customerQty
     * @return float
     */
    private function getCurrentCartItemQty(float $guestQty, float $customerQty): float
    {
        return match ($this->config->getCartMergePreference()) {
            Config::CART_PREFERENCE_CUSTOMER => $customerQty,
            Config::CART_PREFERENCE_GUEST => $guestQty,
            default => $guestQty + $customerQty
        };
    }

    /**
     * Validate product stock availability
     *
     * @param ProductInterface $product
     * @param string $sku
     * @param float $guestQty
     * @param float $customerQty
     * @return bool
     */
    private function validateProductQty(
        ProductInterface $product,
        string $sku,
        float $guestQty,
        float $customerQty
    ): bool {
        $salableQty = $this->stockRegistry->getStockStatus(
            $product->getId(),
            $product->getStore()->getWebsiteId()
        )->getQty();

        $this->cumulativeQty[$sku] ??= 0;
        $this->cumulativeQty[$sku] += $this->getCurrentCartItemQty($guestQty, $customerQty);

        return $salableQty >= $this->cumulativeQty[$sku];
    }

    /**
     * Validate composite product quantities
     *
     * @param ProductInterface $productInterface
     * @param Item $guestCartItem
     * @param Item $customerCartItem
     * @return bool
     */
    private function validateCompositeProductQty(
        ProductInterface $productInterface,
        Item $guestCartItem,
        Item $customerCartItem
    ): bool {
        $guestChildItems = $this->retrieveChildItems($guestCartItem);
        foreach ($customerCartItem->getChildren() as $customerChildItem) {
            $childProduct = $customerChildItem->getProduct()->getOptions()
                ? $this->getProduct((int) $customerChildItem->getProduct()->getId())
                : $customerChildItem->getProduct();
            $sku = $childProduct->getSku();
            $customerItemQty = $customerCartItem->getQty() * $customerChildItem->getQty();
            $guestItemQty = $guestCartItem->getQty() * $guestChildItems[$sku]->getQty();

            if (!$this->validateProductQty($childProduct, $sku, $guestItemQty, $customerItemQty)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get product by ID
     *
     * @param int $productId
     * @return ProductInterface
     * @throws NoSuchEntityException
     */
    private function getProduct(int $productId): ProductInterface
    {
        return $this->productRepository->getById($productId);
    }

    /**
     * Retrieve child items from a quote item
     *
     * @param Item $quoteItem
     * @return Item[]
     */
    private function retrieveChildItems(Item $quoteItem): array
    {
        $childItems = [];
        foreach ($quoteItem->getChildren() as $childItem) {
            $childItems[$childItem->getProduct()->getSku()] = $childItem;
        }
        return $childItems;
    }

    /**
     * Safely delete a cart item by ID
     *
     * @param int $cartId
     * @param int $itemId
     * @return void
     */
    private function safeDeleteCartItem(int $cartId, int $itemId): void
    {
        try {
            $this->cartItemRepository->deleteById($cartId, $itemId);
        } catch (NoSuchEntityException|CouldNotSaveException $e) { // phpcs:ignore
            // Optionally log the error.
        }
    }
}
