<?php
/**
 * Copyright 2016 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

use Magento\Quote\Model\Quote;

/**
 * Test helper for Quote
 *
 * This helper extends the concrete Quote class to provide
 * test-specific functionality without dependency injection issues.
 */
class QuoteTestHelper extends Quote
{
    /**
     * @var array
     */
    private $visibleItems = [];

    /**
     * Constructor that skips parent initialization
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Set super mode
     *
     * @param mixed $value
     * @return $this
     */
    public function setIsSuperMode($value)
    {
        return $this;
    }

    /**
     * Get all visible items
     *
     * @return array
     */
    public function getAllVisibleItems()
    {
        return $this->visibleItems;
    }

    /**
     * Set visible items
     *
     * @param array $items
     * @return $this
     */
    public function setVisibleItems($items)
    {
        $this->visibleItems = $items;
        return $this;
    }
}

