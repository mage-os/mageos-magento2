<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\ConfigurableProduct\Test\Unit\Helper;

use Magento\ConfigurableProduct\Block\Adminhtml\Product\Edit\Tab\Variations\Config;

/**
 * Test helper for Magento\ConfigurableProduct\Block\Adminhtml\Product\Edit\Tab\Variations\Config
 */
class ConfigTestHelper extends Config
{
    private $canEditPrice = false;
    private $canReadPrice = false;
    
    public function __construct()
    {
        // Skip parent constructor to avoid dependencies
    }
    
    /**
     * Set can edit price
     * 
     * @param bool $value
     * @return $this
     */
    public function setCanEditPrice($value)
    {
        $this->canEditPrice = $value;
        return $this;
    }
    
    /**
     * Get can edit price
     * 
     * @return bool
     */
    public function getCanEditPrice()
    {
        return $this->canEditPrice;
    }
    
    /**
     * Set can read price
     * 
     * @param bool $value
     * @return $this
     */
    public function setCanReadPrice($value)
    {
        $this->canReadPrice = $value;
        return $this;
    }
    
    /**
     * Get can read price
     * 
     * @return bool
     */
    public function getCanReadPrice()
    {
        return $this->canReadPrice;
    }
}
