<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Checkout\Test\Unit\Helper;

use Magento\Framework\DataObject;

/**
 * Test double returning qty of 1.
 */
class QtyOneDataObjectTestHelper extends DataObject
{
    /**
     * Constructor intentionally empty for test double.
     */
    public function __construct()
    {
    }

    /**
     * Returns quantity fixed to 1 for test scenario.
     *
     * @return int
     */
    public function getQty()
    {
        return 1;
    }
}
