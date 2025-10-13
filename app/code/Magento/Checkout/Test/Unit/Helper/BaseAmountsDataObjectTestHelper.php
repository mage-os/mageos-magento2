<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Checkout\Test\Unit\Helper;

use Magento\Framework\DataObject;

/**
 * Test double exposing base amounts for subtotal calculations.
 */
class BaseAmountsDataObjectTestHelper extends DataObject
{
    /**
     * Constructor intentionally empty for test double.
     */
    public function __construct()
    {
    }

    /**
     * Base tax amount fixed for test.
     *
     * @return int
     */
    public function getBaseTaxAmount()
    {
        return 0;
    }

    /**
     * Base discount tax compensation fixed for test.
     *
     * @return int
     */
    public function getBaseDiscountTaxCompensation()
    {
        return 0;
    }

    /**
     * Base row total fixed for test.
     *
     * @return int
     */
    public function getBaseRowTotal()
    {
        return 0;
    }
}
