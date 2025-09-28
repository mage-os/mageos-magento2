<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Bundle\Test\Unit\Helper;

use Magento\Bundle\Model\Product\Type;

/**
 * Test helper for Magento\Bundle\Model\Product\Type
 */
class TypeTestHelper extends Type
{
    public function __construct()
    {
        // Skip parent constructor to avoid dependencies
    }
    
    /**
     * Set store filter
     * 
     * @param mixed $storeId
     * @param mixed $product
     * @return $this
     */
    public function setStoreFilter($storeId, $product)
    {
        // Mock implementation
        return $this;
    }
    
    /**
     * Get options IDs
     * 
     * @param mixed $product
     * @return array
     */
    public function getOptionsIds($product)
    {
        return [1, 2];
    }
    
    /**
     * Get options collection
     * 
     * @param mixed $product
     * @return mixed
     */
    public function getOptionsCollection($product)
    {
        return new class {
            public function appendSelections($selections)
            {
                return [];
            }
        };
    }
    
    /**
     * Get selections collection
     * 
     * @param mixed $optionIds
     * @param mixed $product
     * @return mixed
     */
    public function getSelectionsCollection($optionIds, $product)
    {
        return new class {
            public function appendSelections($selections)
            {
                return [];
            }
        };
    }
}
