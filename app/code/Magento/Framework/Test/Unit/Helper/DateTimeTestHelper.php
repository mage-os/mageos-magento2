<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Test\Unit\Helper;

use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Test helper for DateTime
 *
 * This helper extends the concrete DateTime class to provide
 * test-specific functionality without dependency injection issues.
 * 
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class DateTimeTestHelper extends DateTime
{
    /**
     * @var string|null
     */
    private $formattedDate;

    /**
     * Constructor that skips parent initialization
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues in tests
    }

    /**
     * Format date for testing
     *
     * @param string $format
     * @return string|null
     */
    public function format($format)
    {
        return $this->formattedDate;
    }

    /**
     * Set formatted date
     *
     * @param string $date
     * @return $this
     */
    public function setFormattedDate($date)
    {
        $this->formattedDate = $date;
        return $this;
    }

    /**
     * Date method for testing
     *
     * @param string|null $format
     * @param string|null $input
     * @param string|null $timezone
     * @return $this
     */
    public function date($format = null, $input = null, $timezone = null)
    {
        return $this;
    }
}

