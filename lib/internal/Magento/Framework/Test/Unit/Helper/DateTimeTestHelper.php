<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Framework\Test\Unit\Helper;

use Magento\Framework\Stdlib\DateTime;

/**
 * Test helper for DateTime with custom methods
 */
class DateTimeTestHelper extends DateTime
{
    /**
     * @var string
     */
    private $now = '';

    /**
     * Constructor that skips parent dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get current time (custom method for tests)
     *
     * @return string
     */
    public function now()
    {
        return $this->now;
    }

    /**
     * Set now time for testing
     *
     * @param string $dateTime
     * @return $this
     */
    public function setNow(string $dateTime): self
    {
        $this->now = $dateTime;
        return $this;
    }
}
