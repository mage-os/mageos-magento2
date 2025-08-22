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
     * @return void
     * @throws LocalizedException
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSave(
        CartItemRepositoryInterface $subject,
        CartItemInterface $cartItem
    ): void {
        $sku = $cartItem->getSku();
        if (!$sku) {
            return;
        }

        try {
            $quote = $this->cartRepository->getActive($cartItem->getQuoteId());

            foreach ($quote->getAllItems() as $item) {
                if ($sku === $item->getSku()) {
                    $this->checkProductWebsiteAssignment($item->getProductId(), $item->getStoreId());
                    return;
                }
            }

            // Fallback: product not in quote items yet
            $this->checkProductWebsiteAssignmentBySku($sku, $quote->getStoreId());

        } catch (NoSuchEntityException $e) {
            throw new LocalizedException(__('Product that you are trying to add is not available.'));
        }
    }

    /**
     * Check product website assignment by SKU
     *
     * @param string $sku
     * @param int $storeId
     * @throws LocalizedException
     */
    private function checkProductWebsiteAssignmentBySku(string $sku, int $storeId): void
    {
        try {
            $product = $this->productRepository->get($sku, false, $storeId);
            $this->validateWebsiteAssignment($product->getWebsiteIds(), $storeId);
        } catch (NoSuchEntityException $e) {
            throw new LocalizedException(__('Product that you are trying to add is not available.'));
        }
    }

    /**
     * Check product website assignment by product ID
     *
     * @param int $productId
     * @param int|null $storeId
     * @throws LocalizedException
     */
    private function checkProductWebsiteAssignment($productId, $storeId): void
    {
        try {
            $product = $this->productRepository->getById($productId, false, $storeId);
            $this->validateWebsiteAssignment($product->getWebsiteIds(), $storeId);
        } catch (NoSuchEntityException $e) {
            throw new LocalizedException(__('Product that you are trying to add is not available.'));
        }
    }

    /**
     * Validate product website assignment
     *
     * @param array|null $websiteIds
     * @param int $storeId
     * @throws LocalizedException
     */
    private function validateWebsiteAssignment(?array $websiteIds, int $storeId): void
    {
        $websiteId = $this->storeManager->getStore($storeId)->getWebsiteId();

        if (empty($websiteIds) || !in_array($websiteId, $websiteIds, true)) {
            throw new LocalizedException(__('Product that you are trying to add is not available.'));
        }
    }
}
