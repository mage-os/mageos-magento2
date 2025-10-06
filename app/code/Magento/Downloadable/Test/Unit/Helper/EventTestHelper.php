<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Downloadable\Test\Unit\Helper;

use Magento\Framework\Event;

/**
 * Test helper class for Event with custom methods
 */
class EventTestHelper extends Event
{
    /**
     * Skip parent constructor to avoid dependencies
     */
    public function __construct()
    {
        // Skip parent constructor
    }

    /**
     * Custom getStore method for testing
     *
     * @return mixed
     */
    public function getStore()
    {
        return null;
    }

    /**
     * Custom getResult method for testing
     *
     * @return mixed
     */
    public function getResult()
    {
        return null;
    }

    /**
     * Custom getQuote method for testing
     *
     * @return mixed
     */
    public function getQuote()
    {
        return null;
    }

    /**
     * Custom getOrder method for testing
     *
     * @return mixed
     */
    public function getOrder()
    {
        return null;
    }
}
