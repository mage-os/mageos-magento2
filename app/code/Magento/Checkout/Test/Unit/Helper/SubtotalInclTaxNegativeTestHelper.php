<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Checkout\Test\Unit\Helper;

use Magento\Framework\DataObject;

/**
 * Item stub returning missing rowTotalInclTax with separate tax and compensation values.
 */
class SubtotalInclTaxNegativeTestHelper extends DataObject
{
    /** @var int|float */
    private $tax;

    /** @var int|float */
    private $comp;

    /** @var int|float */
    private $row;

    /**
     * @param int|float $tax
     * @param int|float $comp
     * @param int|float $row
     */
    public function __construct($tax, $comp, $row)
    {
        $this->tax = $tax;
        $this->comp = $comp;
        $this->row = $row;
    }

    /**
     * @return false
     */
    public function getRowTotalInclTax()
    {
        return false;
    }

    /**
     * @return int|float
     */
    public function getTaxAmount()
    {
        return $this->tax;
    }

    /**
     * @return int|float
     */
    public function getDiscountTaxCompensation()
    {
        return $this->comp;
    }

    /**
     * @return int|float
     */
    public function getRowTotal()
    {
        return $this->row;
    }
}
