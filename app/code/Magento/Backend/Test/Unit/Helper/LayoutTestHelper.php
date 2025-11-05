<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Helper;

use Magento\Framework\View\Layout;

/**
 * Test helper for Layout
 */
class LayoutTestHelper extends Layout
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
