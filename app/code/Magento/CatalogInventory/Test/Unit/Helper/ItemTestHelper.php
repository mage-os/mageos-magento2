<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogInventory\Test\Unit\Helper;

use Magento\CatalogInventory\Model\Stock\Item;

/**
 * TestHelper for Item
 * Provides implementation for Item with additional test methods
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class ItemTestHelper extends Item
{
    /** @var int|null */
    private $itemId = null;
    /** @var int|null */
    private $productId = null;
    /** @var bool|null */
    private $isInStock = null;
    /** @var bool|null */
    private $stockStatusChangedAuto = null;
    /** @var bool|null */
    private $manageStock = null;
    /** @var int|null */
    private $websiteId = null;
    /** @var int|null */
    private $stockId = null;
    /** @var float|null */
    private $qty = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        // Mock implementation - no parent constructor call
    }

    /**
     * Get item id
     *
     * @return int|null
     */
    public function getItemId()
    {
        return $this->itemId;
    }

    /**
     * Set item id
     *
     * @param int|null $itemId
     * @return $this
     */
    public function setItemId($itemId)
    {
        $this->itemId = $itemId;
        return $this;
    }

    /**
     * Get product id
     *
     * @return int|null
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * Set product id
     *
     * @param int|null $productId
     * @return $this
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;
        return $this;
    }

    /**
     * Get is in stock
     *
     * @return bool|null
     */
    public function getIsInStock()
    {
        return $this->isInStock;
    }

    /**
     * Set is in stock
     *
     * @param bool|null $isInStock
     * @return $this
     */
    public function setIsInStock($isInStock)
    {
        $this->isInStock = $isInStock;
        return $this;
    }

    /**
     * Get stock status changed auto
     *
     * @return bool|null
     */
    public function getStockStatusChangedAuto()
    {
        return $this->stockStatusChangedAuto;
    }

    /**
     * Set stock status changed auto
     *
     * @param bool|null $stockStatusChangedAuto
     * @return $this
     */
    public function setStockStatusChangedAuto($stockStatusChangedAuto)
    {
        $this->stockStatusChangedAuto = $stockStatusChangedAuto;
        return $this;
    }

    /**
     * Get manage stock
     *
     * @return bool|null
     */
    public function getManageStock()
    {
        return $this->manageStock;
    }

    /**
     * Set manage stock
     *
     * @param bool|null $manageStock
     * @return $this
     */
    public function setManageStock($manageStock)
    {
        $this->manageStock = $manageStock;
        return $this;
    }

    /**
     * Get website id
     *
     * @return int|null
     */
    public function getWebsiteId()
    {
        return $this->websiteId;
    }

    /**
     * Set website id
     *
     * @param int|null $websiteId
     * @return $this
     */
    public function setWebsiteId($websiteId)
    {
        $this->websiteId = $websiteId;
        return $this;
    }

    /**
     * Get stock id
     *
     * @return int|null
     */
    public function getStockId()
    {
        return $this->stockId;
    }

    /**
     * Set stock id
     *
     * @param int|null $stockId
     * @return $this
     */
    public function setStockId($stockId)
    {
        $this->stockId = $stockId;
        return $this;
    }

    /**
     * Get qty
     *
     * @return float|null
     */
    public function getQty()
    {
        return $this->qty;
    }

    /**
     * Set qty
     *
     * @param float|null $qty
     * @return $this
     */
    public function setQty($qty)
    {
        $this->qty = $qty;
        return $this;
    }

    /**
     * Set low stock date
     *
     * @param mixed $lowStockDate
     * @return $this
     */
    public function setLowStockDate($lowStockDate)
    {
        return $this;
    }

    /**
     * Set stock status changed automatically flag
     *
     * @param mixed $flag
     * @return $this
     */
    public function setStockStatusChangedAutomaticallyFlag($flag)
    {
        return $this;
    }

    /**
     * Get stock status changed automatically flag
     *
     * @return bool
     */
    public function getStockStatusChangedAutomaticallyFlag()
    {
        return false;
    }
}
