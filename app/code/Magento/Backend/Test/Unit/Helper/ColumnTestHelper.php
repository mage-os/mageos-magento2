<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Helper;

use Magento\Backend\Block\Widget\Grid\Column;

/**
 * Test helper for Column (Grid Column)
 *
 * This helper extends the concrete Column class to provide
 * test-specific functionality without dependency injection issues.
 */
class ColumnTestHelper extends Column
{
    /**
     * @var string
     */
    private $index = 'result_data';

    /**
     * Constructor that skips parent initialization
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get index
     *
     * @return string
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * Set index
     *
     * @param string $index
     * @return $this
     */
    public function setIndex($index)
    {
        $this->index = $index;
        return $this;
    }
}

