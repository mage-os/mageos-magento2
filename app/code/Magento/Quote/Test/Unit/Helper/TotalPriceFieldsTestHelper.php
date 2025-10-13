<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

use Magento\Quote\Model\Quote\Address\Total;

class TotalPriceFieldsTestHelper extends Total
{
    /**
     * Override parent constructor for unit tests.
     */
    public function __construct()
    {
    }

    /**
     * @return int|float
     */
    public function getSubtotal()
    {
        return 0;
    }

    /**
     * @return int|float
     */
    public function getSubtotalInclTax()
    {
        return 0;
    }

    /**
     * @return int|float
     */
    public function getGrandTotal()
    {
        return 0;
    }

    /**
     * @return int|float
     */
    public function getDiscountTaxCompensationAmount()
    {
        return 0;
    }

    /**
     * @return int|float
     */
    public function getDiscountAmount()
    {
        return 0;
    }

    /**
     * @return string
     */
    public function getDiscountDescription()
    {
        return '';
    }

    /**
     * @return array
     */
    public function getAppliedTaxes()
    {
        return [];
    }
}
