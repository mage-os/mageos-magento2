<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */

declare(strict_types=1);

namespace Magento\Quote\Plugin;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Webapi\Rest\Request as RestRequest;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Api\GuestCartItemRepositoryInterface;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Plugin to update cart ID from request and validate product website assignment
 */
class UpdateCartId
{
    /**
     * @param RestRequest $request
     * @param ProductRepositoryInterface $productRepository
     * @param StoreManagerInterface $storeManager
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param CartRepositoryInterface $cartRepository
     */
    public function __construct(
        private readonly RestRequest $request,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly StoreManagerInterface $storeManager,
        private readonly QuoteIdMaskFactory $quoteIdMaskFactory,
        private readonly CartRepositoryInterface $cartRepository
    ) {
    }

    /**
     * Before saving a guest cart item, set quote ID from request and validate website assignment
     *
     * @param GuestCartItemRepositoryInterface $subject
     * @param CartItemInterface $cartItem
     * @return void
     * @throws LocalizedException
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSave(
        GuestCartItemRepositoryInterface $subject,
        CartItemInterface $cartItem
    ): void {
        if ($cartId = $this->request->getParam('cartId')) {
            $cartItem->setQuoteId($cartId);
        }

        $this->validateProductWebsiteAssignment($cartItem);
    }

    /**
     * Validate product's website assignment for guest cart item
     *
     * @param CartItemInterface $cartItem
     * @return void
     * @throws LocalizedException
     */
    private function validateProductWebsiteAssignment(CartItemInterface $cartItem): void
    {
        $sku = $cartItem->getSku();
        if (!$sku) {
            return;
        }

        $storeId = (int)($cartItem->getStoreId() ?? 0);
        if (!$storeId) {
            $maskedQuoteId = $cartItem->getQuoteId();
            $quoteIdMask = $this->quoteIdMaskFactory->create()->load($maskedQuoteId, 'masked_id');
            $quoteId = (int)$quoteIdMask->getQuoteId();
            if (!$quoteId) {
                return;
            }
            try {
                $quote = $this->cartRepository->get($quoteId);
                $storeId = (int)$quote->getStoreId();
            } catch (NoSuchEntityException) {
                throw new LocalizedException(__('Product that you are trying to add is not available.'));
            }
        }

        $this->validateWebsiteAssignmentBySku($sku, $storeId);
    }

    /**
     * Validate by SKU for new items
     *
     * @param string $sku
     * @param int $storeId
     * @return void
     * @throws LocalizedException
     */
    private function validateWebsiteAssignmentBySku(string $sku, int $storeId): void
    {
        try {
            $product = $this->productRepository->get($sku, false, $storeId);
            $this->checkProductInWebsite($product->getWebsiteIds(), $storeId);
        } catch (NoSuchEntityException) {
            throw new LocalizedException(__('Product that you are trying to add is not available.'));
        }
    }

    /**
     * Validate by product ID for existing items
     *
     * @param array|null $websiteIds
     * @param int $storeId
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function checkProductInWebsite(?array $websiteIds, int $storeId): void
    {
        $websiteId = $this->storeManager->getStore($storeId)->getWebsiteId();

        if (empty($websiteIds) || !in_array($websiteId, $websiteIds, true)) {
            throw new LocalizedException(__('Product that you are trying to add is not available.'));
        }
    }
}
