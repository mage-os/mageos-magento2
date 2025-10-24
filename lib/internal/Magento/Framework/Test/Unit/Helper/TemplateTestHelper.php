<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Test\Unit\Helper;

use Magento\Framework\View\Element\Template;

/**
 * Test helper for Template
 *
 * This helper extends the concrete Template class to provide
 * test-specific functionality without dependency injection issues.
 */
class TemplateTestHelper extends Template
{
    /**
     * Constructor that skips parent initialization
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Set item
     *
     * @param mixed $item
     * @return $this
     */
    public function setItem($item)
    {
        return $this;
    }

    /**
     * Render HTML
     *
     * @return string
     */
    public function toHtml()
    {
        return 'rendered';
    }
}

