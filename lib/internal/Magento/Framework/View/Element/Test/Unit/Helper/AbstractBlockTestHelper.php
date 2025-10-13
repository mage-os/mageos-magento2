<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\View\Element\Test\Unit\Helper;

use Magento\Framework\View\Element\AbstractBlock;

/**
 * Test helper for Magento\Framework\View\Element\AbstractBlock
 * 
 * Provides test-specific methods for AbstractBlock without complex constructor dependencies.
 * Note: setNameInLayout already exists in parent AbstractBlock (line 326)
 */
class AbstractBlockTestHelper extends AbstractBlock
{
    /**
     * @var string
     */
    private $nameInLayout = '';
    
    /**
     * @var mixed
     */
    private $productEntity = null;
    
    /**
     * @var bool
     */
    private $isReadonly = false;
    
    /**
     * @var array
     */
    private $configOptions = [];
    
    /**
     * @var array
     */
    private $fieldDependencies = [];
    
    /**
     * @var bool
     */
    private $canEditPrice = false;
    
    public function __construct()
    {
        // Skip parent constructor to avoid dependencies
    }
    
    /**
     * Set name in layout
     * Note: This method exists in parent AbstractBlock (line 326)
     * but we override for testing to avoid layout dependency
     *
     * @param string $name
     * @return $this
     */
    public function setNameInLayout($name)
    {
        $this->nameInLayout = $name;
        return $this;
    }
    
    /**
     * Get name in layout
     *
     * @return string
     */
    public function getNameInLayout()
    {
        return $this->nameInLayout;
    }
    
    /**
     * Set product entity
     *
     * @param mixed $entity
     * @return $this
     */
    public function setProductEntity($entity)
    {
        $this->productEntity = $entity;
        return $this;
    }
    
    /**
     * Get product entity
     *
     * @return mixed
     */
    public function getProductEntity()
    {
        return $this->productEntity;
    }
    
    /**
     * Set is readonly
     *
     * @param bool $readonly
     * @return $this
     */
    public function setIsReadonly($readonly)
    {
        $this->isReadonly = $readonly;
        return $this;
    }
    
    /**
     * Is readonly
     *
     * @return bool
     */
    public function isReadonly()
    {
        return $this->isReadonly;
    }
    
    /**
     * Add config options
     *
     * @param array $options
     * @return $this
     */
    public function addConfigOptions($options)
    {
        $this->configOptions = array_merge($this->configOptions, $options);
        return $this;
    }
    
    /**
     * Get config options
     *
     * @return array
     */
    public function getConfigOptions()
    {
        return $this->configOptions;
    }
    
    /**
     * Add field dependence
     *
     * @param string $field
     * @param string $dependence
     * @return $this
     */
    public function addFieldDependence($field, $dependence)
    {
        $this->fieldDependencies[$field] = $dependence;
        return $this;
    }
    
    /**
     * Get field dependencies
     *
     * @return array
     */
    public function getFieldDependencies()
    {
        return $this->fieldDependencies;
    }
    
    /**
     * Set can edit price
     *
     * @param bool $canEdit
     * @return $this
     */
    public function setCanEditPrice($canEdit)
    {
        $this->canEditPrice = $canEdit;
        return $this;
    }
    
    /**
     * Can edit price
     *
     * @return bool
     */
    public function canEditPrice()
    {
        return $this->canEditPrice;
    }
}

