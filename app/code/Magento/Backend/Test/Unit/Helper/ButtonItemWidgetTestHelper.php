<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Helper;

use Magento\Backend\Block\Widget\Button\Item as ButtonItemWidget;

/**
 * Test helper for Magento\Backend\Block\Widget\Button\Item
 */
class ButtonItemWidgetTestHelper extends ButtonItemWidget
{
    /**
     * @var string
     */
    private $id = 'default';
    
    public function __construct()
    {
        // Skip parent constructor to avoid dependencies
    }
    
    /**
     * Get ID
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Set ID
     *
     * @param string $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }
}
