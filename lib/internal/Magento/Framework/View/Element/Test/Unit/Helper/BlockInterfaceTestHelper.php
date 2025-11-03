<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\View\Element\Test\Unit\Helper;

use Magento\Framework\View\Element\AbstractBlock;

/**
 * Test helper for BlockInterface with additional test methods
 */
class BlockInterfaceTestHelper extends AbstractBlock
{
    /**
     * Bypass parent constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get widget values
     *
     * @return array|null
     */
    public function getWidgetValues()
    {
        return $this->getData('widget_values');
    }

    /**
     * Set widget values
     *
     * @param array $values
     * @return $this
     */
    public function setWidgetValues(array $values)
    {
        return $this->setData('widget_values', $values);
    }
}
