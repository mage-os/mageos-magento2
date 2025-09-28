<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Bundle\Test\Unit\Helper;

use Magento\Bundle\Model\Selection;

/**
 * Test helper for Magento\Bundle\Model\Selection
 */
class SelectionTestHelper extends Selection
{
    private $productId = null;
    private $selectionPriceType = null;
    private $selectionPriceValue = null;
    
    public function __construct()
    {
        // Skip parent constructor to avoid dependencies
    }
    
    /**
     * Set product ID
     * 
     * @param mixed $productId
     * @return $this
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;
        return $this;
    }
    
    /**
     * Get product ID
     * 
     * @return mixed
     */
    public function getProductId()
    {
        return $this->productId;
    }
    
    /**
     * Set selection price type
     * 
     * @param mixed $priceType
     * @return $this
     */
    public function setSelectionPriceType($priceType)
    {
        $this->selectionPriceType = $priceType;
        return $this;
    }
    
    /**
     * Get selection price type
     * 
     * @return mixed
     */
    public function getSelectionPriceType()
    {
        return $this->selectionPriceType;
    }
    
    /**
     * Set selection price value
     * 
     * @param mixed $priceValue
     * @return $this
     */
    public function setSelectionPriceValue($priceValue)
    {
        $this->selectionPriceValue = $priceValue;
        return $this;
    }
    
    /**
     * Get selection price value
     * 
     * @return mixed
     */
    public function getSelectionPriceValue()
    {
        return $this->selectionPriceValue;
    }
}
