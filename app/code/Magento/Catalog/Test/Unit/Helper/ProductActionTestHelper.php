<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Model\Product\Action;

/**
 * TestHelper for Product Action with dynamic methods
 */
class ProductActionTestHelper extends Action
{
    /** @var array */
    private $attributesData = [];
    /** @var array */
    private $productIds = [];

    public function __construct()
    {
        // Skip parent constructor to avoid complex dependencies
    }

    public function getAttributesData()
    {
        return $this->attributesData;
    }

    public function setAttributesData($value)
    {
        $this->attributesData = $value;
        return $this;
    }

    public function getProductIds()
    {
        return $this->productIds;
    }

    public function setProductIds($value)
    {
        $this->productIds = $value;
        return $this;
    }
}
