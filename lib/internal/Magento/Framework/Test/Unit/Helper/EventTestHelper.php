<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Framework\Test\Unit\Helper;

use Magento\Framework\Event;

/**
 * Test helper for Event with custom methods
 */
class EventTestHelper extends Event
{
    /**
     * Constructor that skips parent dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get customer (custom method for tests)
     *
     * @return mixed
     */
    public function getCustomer()
    {
        return null;
    }

    /**
     * Get customer data object (custom method for tests)
     *
     * @return mixed
     */
    public function getCustomerDataObject()
    {
        return null;
    }

    /**
     * Get original customer data object (custom method for tests)
     *
     * @return mixed
     */
    public function getOrigCustomerDataObject()
    {
        return null;
    }
}

