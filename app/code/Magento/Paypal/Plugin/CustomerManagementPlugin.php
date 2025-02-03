<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Paypal\Plugin;

use Magento\Quote\Model\CustomerManagement;
use Magento\Quote\Model\Quote as QuoteEntity;
use Magento\Paypal\Model\Config as PaymentMethodConfig;

/**
 * Skip billing address validation for PayPal payment method
 */
class CustomerManagementPlugin
{
    /**
     * Around plugin for the validateAddresses method
     *
     * @param CustomerManagement $subject
     * @param \Closure $proceed
     * @param QuoteEntity $quote
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundValidateAddresses(CustomerManagement $subject, \Closure $proceed, QuoteEntity $quote)
    {
        if ($quote->getCustomerIsGuest() &&
            in_array($quote->getPayment()->getMethod(), PaymentMethodConfig::PAYMENT_METHODS_SKIP_ADDRESS_VALIDATION)) {
            return;
        }
        $proceed($quote);
    }
}
