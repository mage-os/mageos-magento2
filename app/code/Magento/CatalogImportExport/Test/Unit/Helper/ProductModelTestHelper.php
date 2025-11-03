<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogImportExport\Test\Unit\Helper;

use Magento\Catalog\Model\Product;

/**
 * Test helper for Product model - extends concrete implementation
 * 
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class ProductModelTestHelper extends Product
{
    /**
     * @var array
     */
    private $productEntitiesInfo = [];

    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Override to return test data without database dependency
     * 
     * @param array|null $columns
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getProductEntitiesInfo($columns = null)
    {
        return $this->productEntitiesInfo;
    }

    /**
     * @param array $products
     * @return $this
     */
    public function setProductEntitiesInfo($products)
    {
        $this->productEntitiesInfo = $products;
        return $this;
    }
}

