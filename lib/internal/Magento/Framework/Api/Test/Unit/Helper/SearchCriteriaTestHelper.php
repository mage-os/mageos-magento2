<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Api\Test\Unit\Helper;

use Magento\Framework\Api\SearchCriteria;

/**
 * Test helper for SearchCriteria
 *
 * Extends SearchCriteria to add custom methods for testing
 */
class SearchCriteriaTestHelper extends SearchCriteria
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * Constructor - skip parent to avoid dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get items
     *
     * @return array
     */
    public function getItems()
    {
        return $this->data['items'] ?? [];
    }

    /**
     * Set items
     *
     * @param array $items
     * @return $this
     */
    public function setItems(array $items)
    {
        $this->data['items'] = $items;
        return $this;
    }
}
