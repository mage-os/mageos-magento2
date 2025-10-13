<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

use Magento\Quote\Model\Quote\Address\Total;

class TotalShippingTestHelper extends Total
{
    public function __construct()
    {
        // Skip parent constructor
    }

    public function setShippingAmount($amount)
    {
        $this->setTotalAmount('shipping', $amount);
        return $this;
    }

    public function setBaseShippingAmount($amount)
    {
        $this->setBaseTotalAmount('shipping', $amount);
        return $this;
    }

    public function setShippingDescription($description)
    {
        $this->setData('shipping_description', (string)$description);
        return $this;
    }

    public function getShippingDescription()
    {
        return $this->getData('shipping_description');
    }
}
