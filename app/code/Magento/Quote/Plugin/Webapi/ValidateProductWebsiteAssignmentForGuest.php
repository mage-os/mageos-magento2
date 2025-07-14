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
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Api\GuestCartItemRepositoryInterface;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Plugin to validate product website assignment for guest cart operations
 */
class ValidateProductWebsiteAssignmentForGuest
{
    /**
     * @param ProductRepositoryInterface $productRepository
     * @param StoreManagerInterface $storeManager
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     */
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly StoreManagerInterface      $storeManager,
        private readonly QuoteIdMaskFactory         $quoteIdMaskFactory
    ) {
    }

    /**
     * Validate product website assignment before saving guest cart item
     *
     * @param GuestCartItemRepositoryInterface $subject
     * @param CartItemInterface $cartItem
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSave(
        GuestCartItemRepositoryInterface $subject,
        CartItemInterface                $cartItem
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

        // Get current website ID from the masked cart ID
        $maskedQuoteId = $cartItem->getQuoteId();
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($maskedQuoteId, 'masked_id');

        if (!$quoteIdMask->getQuoteId()) {
            return; // Let other validations handle invalid cart
        }

        $currentWebsiteId = $this->storeManager->getStore()->getWebsiteId();

        try {
            $product = $this->productRepository->get($sku, false, null);

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
