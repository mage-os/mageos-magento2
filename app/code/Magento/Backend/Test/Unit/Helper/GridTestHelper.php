<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Helper;

use Magento\Backend\Block\Widget\Grid;

/**
 * Test helper for Grid
 */
class GridTestHelper extends Grid
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
     * helper (custom method for testing)
     *
     * @return mixed
     */
    public function helper()
    {
        return $this->data['helper'] ?? null;
    }
}
