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
    /**
     * @var bool
     */
    private $canEditPrice = false;
    
    /**
     * @var bool
     */
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
     * Is can edit price
     *
     * @return bool
     */
    public function isCanEditPrice()
    {
        return $this->canEditPrice;
    }
    
    /**
     * Get can edit price (alias for compatibility)
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getCanEditPrice()
    {
        return $this->isCanEditPrice();
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
     * Is can read price
     *
     * @return bool
     */
    public function isCanReadPrice()
    {
        return $this->canReadPrice;
    }
    
    /**
     * Get can read price (alias for compatibility)
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getCanReadPrice()
    {
        return $this->isCanReadPrice();
    }
}
