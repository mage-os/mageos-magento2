<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Helper;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Test helper for DateTimeFormatterInterface
 */
class DateTimeFormatterInterfaceTestHelper extends DateTimeFormatterInterface
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * Skip parent constructor
     */
    public function __construct()
    {
        // Skip parent constructor
    }

    /**
     * getTimezone (custom method for testing)
     *
     * @return mixed
     */
    public function getTimezone()
    {
        return $this->data['timezone'] ?? null;
    }

    /**
     * getFilterTime (custom method for testing)
     *
     * @return mixed
     */
    public function getFilterTime()
    {
        return $this->data['filterTime'] ?? null;
    }
}
