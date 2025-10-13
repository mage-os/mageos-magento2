<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Checkout\Test\Unit\Helper;

use Magento\Quote\Model\Quote\Address\Rate;

/**
 * Test helper to expose a Rate with a fixed price for shipping price tests.
 */
class RatePriceTestHelper extends Rate
{
    /** @var int|float */
    private $price;

    /**
     * @param int|float $price
     */
    public function __construct($price)
    {
        $this->price = $price;
    }

    /**
     * @return int|float
     */
    public function getPrice()
    {
        return $this->price;
    }
}
