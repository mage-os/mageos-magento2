<?php
/**
 * Copyright 2016 Adobe
 * All Rights Reserved.
 */

namespace Magento\CatalogInventory\Test\Unit\Helper;

use Magento\CatalogInventory\Model\Stock\Item as StockItem;

/**
 * TestHelper for Stock Item with validator-specific dynamic methods
 */
class StockItemTestHelperForValidator extends StockItem
{
    /** @var mixed */
    private $stockStatus = null;
    /** @var bool */
    private $isInStock = false;

    public function __construct()
    {
        // Skip parent constructor to avoid complex dependencies
    }

    public function getStockStatus()
    {
        return $this->stockStatus;
    }

    public function setStockStatus($status)
    {
        $this->stockStatus = $status;
        return $this;
    }

    public function getIsInStock()
    {
        return $this->isInStock;
    }

    public function setIsInStock($isInStock)
    {
        $this->isInStock = $isInStock;
        return $this;
    }
}
