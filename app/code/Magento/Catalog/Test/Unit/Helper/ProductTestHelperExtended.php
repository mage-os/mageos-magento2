<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Model\Product;

/**
 * TestHelper for Product with constructor parameter and dynamic methods
 */
class ProductTestHelperExtended extends Product
{
    /** @var array */
    private $attributesByCode = [];
    /** @var array */
    private $attributeSelect = [];
    /** @var array */
    private $dataValues = [];
    /** @var int */
    private $dataCallCount = 0;
    /** @var mixed */
    private $resource = null;

    public function __construct($resource = null)
    {
        // Skip parent constructor to avoid complex dependencies
        $this->resource = $resource;
    }

    // Dynamic methods from addMethods
    public function addAttributeToSelect($attribute)
    {
        $this->attributeSelect[] = $attribute;
        return $this;
    }

    public function getAttributesByCode()
    {
        return $this->attributesByCode;
    }

    public function setAttributesByCode($value)
    {
        $this->attributesByCode = $value;
        return $this;
    }

    public function setDataValues($values)
    {
        $this->dataValues = $values;
        $this->dataCallCount = 0;
        return $this;
    }

    // Methods from onlyMethods
    public function __wakeUp()
    {
        // Implementation for __wakeUp method
    }

    public function hasData($key = null)
    {
        return true; // Default for testing
    }

    public function getData($key = '', $index = null)
    {
        if ($key === '') {
            return $this->dataValues;
        }
        
        // Handle sequential calls with data values array
        if ($this->dataValues && $this->dataCallCount < count($this->dataValues)) {
            $result = $this->dataValues[$this->dataCallCount];
            $this->dataCallCount++;
            return $result;
        }
        
        // Handle direct data storage
        if (isset($this->attributesByCode[$key])) {
            return $this->attributesByCode[$key];
        }
        
        return null;
    }

    public function setData($key, $value = null)
    {
        if (is_array($key)) {
            $this->attributesByCode = $key;
        } else {
            $this->attributesByCode[$key] = $value;
        }
        return $this;
    }

    public function unsetData($key = null)
    {
        if ($key === null) {
            $this->attributesByCode = [];
        } else {
            unset($this->attributesByCode[$key]);
        }
        return $this;
    }

    public function getId()
    {
        return 1; // Default ID for testing
    }

    public function getStoreId()
    {
        return 1; // Default store ID for testing
    }

    public function getResource()
    {
        return $this->resource; // Return the passed resource
    }
}
