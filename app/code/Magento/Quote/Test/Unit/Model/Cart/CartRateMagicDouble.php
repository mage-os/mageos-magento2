<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */

declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Model\Cart;

use Magento\Quote\Model\Quote\Address\Rate;

class CartRateMagicDouble extends Rate
{
    private $price;
    private $carrier;
    private $method;
    private $carrierTitle;
    private $methodTitle;

    public function __construct()
    {
        // Skip parent constructor
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function getCarrier()
    {
        return $this->carrier;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getCarrierTitle()
    {
        return $this->carrierTitle;
    }

    public function getMethodTitle()
    {
        return $this->methodTitle;
    }

    public function __set(string $name, $value): void
    {
        $this->$name = $value;
    }
}



