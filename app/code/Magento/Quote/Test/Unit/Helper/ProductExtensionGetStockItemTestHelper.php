<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

/**
 * Lightweight helper to provide getStockItem()/setStockItem() for product extension attributes in tests.
 */
class ProductExtensionGetStockItemTestHelper
{
    /**
     * @var mixed
     */
    private $stockItem;

    /**
     * Get stock item set on the helper.
     *
     * @return mixed
     */
    public function getStockItem()
    {
        return $this->stockItem;
    }

    /**
     * Set stock item to be returned by getStockItem().
     *
     * @param mixed $stockItem
     * @return $this
     */
    public function setStockItem($stockItem)
    {
        $this->stockItem = $stockItem;
        return $this;
    }
}
