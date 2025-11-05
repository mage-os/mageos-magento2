<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Helper;

use Magento\Backend\Block\Widget;

/**
 * Test helper for Widget
 */
class WidgetTestHelper extends Widget
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
     * decorate (custom method for testing)
     *
     * @return mixed
     */
    public function decorate()
    {
        return $this->data['decorate'] ?? null;
    }
}
