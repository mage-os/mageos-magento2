<?php
/**
 * Copyright 2022 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Checkout\Plugin\Api;

use Magento\Checkout\Helper\Data as CheckoutHelper;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\GuestBillingAddressManagementInterface;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;

class VerifyIsGuestCheckoutEnabledBeforeAssignBillingAddress
{
    /**
     * @var CheckoutHelper
     */
    private CheckoutHelper $checkoutHelper;

    /**
     * @var QuoteIdMaskFactory
     */
    private QuoteIdMaskFactory $quoteIdMaskFactory;

    /**
     * @var CartRepositoryInterface
     */
    private CartRepositoryInterface $cartRepository;

    /**
     * @param CheckoutHelper $checkoutHelper
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param CartRepositoryInterface $cartRepository
     */
    public function __construct(
        CheckoutHelper $checkoutHelper,
        QuoteIdMaskFactory $quoteIdMaskFactory,
        CartRepositoryInterface $cartRepository
    ) {
        $this->checkoutHelper = $checkoutHelper;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->cartRepository = $cartRepository;
    }

    /**
     * Checks whether guest checkout is enabled before assigning billing address
     *
     * @param GuestBillingAddressManagementInterface $subject
     * @param string $cartId
     * @param AddressInterface $address
     * @param bool $useForShipping
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeAssign(
        GuestBillingAddressManagementInterface $subject,
        $cartId,
        AddressInterface $address,
        $useForShipping = false
    ): void {
        /** @var $quoteIdMask QuoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        $quote = $this->cartRepository->get($quoteIdMask->getQuoteId());
        if (!$this->checkoutHelper->isAllowedGuestCheckout($quote)) {
            throw new CouldNotSaveException(__('Sorry, guest checkout is not available.'));
        }
    }
}
