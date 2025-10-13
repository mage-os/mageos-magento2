<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

class ShippingMethodDataTestHelper
{
    public function getQuoteCurrencyCode()
    {
        return 'USD';
    }

    public function getMethodTitle()
    {
        return 'method_title';
    }

    public function getCarrierTitle()
    {
        return 'carrier_title';
    }

    public function getPriceExclTax()
    {
        return 'PriceExclTax';
    }

    public function getPriceInclTax()
    {
        return 'PriceInclTax';
    }
}
