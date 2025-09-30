<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Checkout\Test\Unit\Helper;

use Magento\Framework\DataObject;

/**
 * Item stub providing context for calculating price incl tax when missing on item.
 */
class ItemPriceInclTaxContextTestHelper extends DataObject
{
    /** @var int|float */
    private $qty;

    /** @var int|float */
    private $tax;

    /** @var int|float */
    private $comp;

    /** @var int|float */
    private $row;

    /**
     * @param int|float $qty
     * @param int|float $tax
     * @param int|float $comp
     * @param int|float $row
     */
    public function __construct($qty, $tax, $comp, $row)
    {
        $this->qty = $qty;
        $this->tax = $tax;
        $this->comp = $comp;
        $this->row = $row;
    }

    /**
     * @return false
     */
    public function getPriceInclTax()
    {
        return false;
    }

    /**
     * @return int|float
     */
    public function getQty()
    {
        return $this->qty;
    }

    /**
     * @return null
     */
    public function getQtyOrdered()
    {
        return null;
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


