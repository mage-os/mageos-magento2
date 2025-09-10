<?php
/**
 * Copyright 2022 Adobe
 * All Rights Reserved.
 */

declare(strict_types=1);

namespace Magento\SalesRule\Model\Quote;

use Magento\SalesRule\Model\Coupon\Massgenerator;

/**
 * The class to get the coupon code length.
 */
class GetCouponCodeLength extends Massgenerator implements GetCouponCodeLengthInterface
{
    /**
     * Fetch the coupon code length.
     *
     * @param array $couponCodeDataArray
     * @return int
     */
    public function fetchCouponCodeLength(array $couponCodeDataArray): int
    {
        $this->setData($couponCodeDataArray);
        $this->increaseLength();
        return (int)$this->getLength();
    }
}
