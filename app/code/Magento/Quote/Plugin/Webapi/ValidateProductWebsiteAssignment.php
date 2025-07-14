<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Plugin\Webapi;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartItemRepositoryInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Plugin to validate product website assignment for REST API cart operations
 */
class ValidateProductWebsiteAssignment
{
    /**
     * @param ProductRepositoryInterface $productRepository
     * @param StoreManagerInterface $storeManager
     * @param CartRepositoryInterface $cartRepository
     */
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly StoreManagerInterface $storeManager,
        private readonly CartRepositoryInterface $cartRepository
    ) {
    }

    /**
     * Validate product website assignment before saving cart item
     *
     * @param CartItemRepositoryInterface $subject
     * @param CartItemInterface $cartItem
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSave(
        CartItemRepositoryInterface $subject,
        CartItemInterface $cartItem
    ): array {
        $this->validateProductWebsiteAssignment($cartItem);
        return [$cartItem];
    }

    /**
     * Validate that product is assigned to the current website
     *
     * @param CartItemInterface $cartItem
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function validateProductWebsiteAssignment(CartItemInterface $cartItem): void
    {
        $sku = $cartItem->getSku();
        if (!$sku) {
            return;
        }

        // Get current website ID from the cart's store
        $quote = $this->cartRepository->get($cartItem->getQuoteId());
        $storeId = $quote->getStoreId();
        $currentWebsiteId = $this->storeManager->getStore($storeId)->getWebsiteId();

        try {
            // Load product to check website assignment
            $product = $this->productRepository->get($sku, false, $storeId);

            $productWebsiteIds = $product->getWebsiteIds();

            // Validate website assignment
            if (!is_array($productWebsiteIds) || !in_array($currentWebsiteId, $productWebsiteIds)) {
                throw new LocalizedException(
                    __('Product that you are trying to add is not available.')
                );
            }
        } catch (NoSuchEntityException $e) {
            throw new LocalizedException(
                __('Product that you are trying to add is not available.')
            );
        }
    }
}
