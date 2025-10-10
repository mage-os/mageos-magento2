<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

use Magento\Quote\Model\Quote\Address;

class AddressForShippingTestHelper extends Address
{
    public function __construct()
    {
        // Skip parent constructor
    }

    public function setWeight($weight)
    {
        $this->setData('weight', $weight);
        return $this;
    }

    public function setFreeMethodWeight($weight)
    {
        $this->setData('free_method_weight', $weight);
        return $this;
    }

    public function getWeight()
    {
        return $this->getData('weight');
    }

    public function getFreeMethodWeight()
    {
        return $this->getData('free_method_weight');
    }

    public function setFreeShipping($flag)
    {
        $this->setData('free_shipping', (int)$flag);
        return $this;
    }

    public function getFreeShipping()
    {
        return (bool)$this->getData('free_shipping');
    }

    public function setItemQty($qty)
    {
        $this->setData('item_qty', $qty);
        return $this;
    }

    public function setShippingDescription($desc)
    {
        $this->setData('shipping_description', (string)$desc);
        return $this;
    }

    public function getShippingDescription()
    {
        return (string)$this->getData('shipping_description');
    }

    public function setTotalQty($qty)
    {
        $this->setData('total_qty', $qty);
        return $this;
    }

    public function getTotalQty()
    {
        return (int)$this->getData('total_qty');
    }
}


