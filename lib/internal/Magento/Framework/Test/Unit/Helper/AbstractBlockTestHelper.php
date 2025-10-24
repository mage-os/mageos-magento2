<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Test\Unit\Helper;

use Magento\Framework\View\Element\AbstractBlock;

/**
 * Test helper for AbstractBlock
 *
 * This helper extends the concrete AbstractBlock class to provide
 * test-specific functionality without dependency injection issues.
 */
class AbstractBlockTestHelper extends AbstractBlock
{
    /**
     * Constructor that skips parent initialization
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Set additional message
     *
     * @param string $message
     * @return $this
     */
    public function setAdditionalMessage($message)
    {
        return $this;
    }

    /**
     * Set is recalculated
     *
     * @return $this
     */
    public function setIsRecalculated()
    {
        return $this;
    }

    /**
     * Convert to HTML
     *
     * @return string
     */
    public function toHtml()
    {
        return '<span>test Message</span>';
    }
}

