<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Checkout\Test\Unit\Helper;

use Magento\Framework\DataObject;

/**
 * Item stub returning a fixed row total including tax.
 */
class RowTotalInclTaxFixedTestHelper extends DataObject
{
    /** @var int|float */
    private $row;

    /**
     * @param int|float $row
     */
    public function __construct($row)
    {
        $this->row = $row;
    }

    /**
     * @return int|float
     */
    public function getRowTotalInclTax()
    {
        return $this->row;
    }
}
