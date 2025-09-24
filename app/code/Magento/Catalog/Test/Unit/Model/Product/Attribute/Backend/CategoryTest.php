<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Model\Product\Attribute\Backend;

use Magento\Catalog\Model\Product\Attribute\Backend\Category;
use Magento\Framework\DataObject;
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase
{
    public function testAfterLoad()
    {
        $categoryIds = [1, 2, 3, 4, 5];

        $product = new class extends DataObject {
            private $categoryIds = null;
            private $data = [];
            
            public function __construct()
            {
                // Don't call parent constructor to avoid dependencies
            }
            
            public function getCategoryIds()
            {
                return $this->categoryIds;
            }
            
            public function setCategoryIds($categoryIds)
            {
                $this->categoryIds = $categoryIds;
                return $this;
            }
            
            public function setData($key, $value = null)
            {
                if (is_array($key)) {
                    $this->data = array_merge($this->data, $key);
                } else {
                    $this->data[$key] = $value;
                }
                return $this;
            }
            
            public function getData($key = '', $index = null)
            {
                if ($key === '') {
                    return $this->data;
                }
                return isset($this->data[$key]) ? $this->data[$key] : null;
            }
        };
        $product->setCategoryIds($categoryIds);

        $categoryAttribute = new class extends DataObject {
            private $attributeCode = null;
            
            public function __construct()
            {
                // Don't call parent constructor to avoid dependencies
            }
            
            public function getAttributeCode()
            {
                return $this->attributeCode;
            }
            
            public function setAttributeCode($attributeCode)
            {
                $this->attributeCode = $attributeCode;
                return $this;
            }
        };
        $categoryAttribute->setAttributeCode('category_ids');

        $model = new Category();
        $model->setAttribute($categoryAttribute);

        $model->afterLoad($product);
        
        // Verify that the product data was set correctly
        $this->assertEquals($categoryIds, $product->getData('category_ids'));
    }
}
