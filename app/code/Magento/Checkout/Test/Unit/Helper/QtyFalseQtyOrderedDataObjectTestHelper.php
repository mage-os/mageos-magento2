<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Checkout\Test\Unit\Helper;

use Magento\Framework\DataObject;

/**
 * Test double exposing false qty and fixed qty ordered.
 */
class QtyFalseQtyOrderedDataObjectTestHelper extends DataObject
{
    /**
     * Constructor intentionally empty for test double.
     */
    public function __construct()
    {
    }

    /**
     * Returns false to emulate absence of quantity.
     *
     * @return bool
     */
    public function getQty()
    {
        return false;
    }

    /**
     * Returns a fixed ordered quantity for test.
     *
     * @return float
     */
    public function getQtyOrdered()
    {
        return 5.5;
    }
}
