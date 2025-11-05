<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogInventory\Test\Unit\Helper;

use Magento\CatalogInventory\Model\Stock\Item;

/**
 * Test helper for StockItem - extends concrete implementation
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class StockItemTestHelper extends Item
{
    /**
     * @var array
     */
    private $methods = [];

    /**
     * @var int|null
     */
    private $stockId = null;

    public function __construct(array $methods = [], $stockId = null)
    {
        $this->methods = $methods;
        $this->stockId = $stockId;
        // Skip parent constructor to avoid dependency injection issues
        // All StockItemInterface methods are inherited from parent
    }

    public function getItemId()
    {
        return $this->stockId;
    }

    /**
     * Custom method for tests
     * @param string|null $fieldName
     * @return mixed
     */
    public function getField($fieldName = null)
    {
        return in_array('getField', $this->methods) ? 'call-method' : null;
    }
}

