<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Test\Unit\Helper;

use Magento\Framework\Filter\FilterManager;

/**
 * Test helper for FilterManager
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class FilterManagerTestHelper extends FilterManager
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Truncate string
     *
     * @param string $string
     * @param int $length
     * @param string $etc
     * @param bool $breakWords
     * @param bool $middle
     * @return string
     */
    public function truncate($string, $length = 80, $etc = '...', $breakWords = true, $middle = false)
    {
        return $string;
    }

    /**
     * Get factories
     *
     * @return array
     */
    public function getFactories()
    {
        return [];
    }
}
