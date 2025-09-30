<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Checkout\Test\Unit\Helper;

use Magento\Framework\DataObject;

/**
 * Item stub that returns a fixed price including tax.
 */
class ItemPriceInclTaxFixedTestHelper extends DataObject
{
    /** @var int|float */
    private $value;

    /**
     * @param int|float $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return int|float
     */
    public function getPriceInclTax()
    {
        return $this->value;
    }
}


