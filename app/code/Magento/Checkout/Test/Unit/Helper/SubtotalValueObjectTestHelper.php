<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Checkout\Test\Unit\Helper;

use Magento\Framework\DataObject;

/**
 * Simple value object for a subtotal total segment used in Checkout tests.
 */
class SubtotalValueObjectTestHelper extends DataObject
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
    public function getValue()
    {
        return $this->value;
    }
}
