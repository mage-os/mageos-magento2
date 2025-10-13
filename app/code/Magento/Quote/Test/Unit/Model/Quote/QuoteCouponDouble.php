<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Model\Quote;

use Magento\Quote\Model\Quote;

class QuoteCouponDouble extends Quote
{
    public function __construct()
    {
        // Skip parent constructor
    }

    public function setCouponCode($code)
    {
        $this->setData('coupon_code', $code);
        return $this;
    }

    public function getCouponCode()
    {
        return $this->getData('coupon_code');
    }
}


