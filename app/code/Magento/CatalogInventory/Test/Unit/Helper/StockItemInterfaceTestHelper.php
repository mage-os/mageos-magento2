<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogInventory\Test\Unit\Helper;

use Magento\CatalogInventory\Model\Stock\Item;

/**
 * TestHelper for StockItemInterface
 * @SuppressWarnings(PHPMD.BooleanGetMethodName)
 */
class StockItemInterfaceTestHelper extends Item
{
    /** @var bool */
    private $hasAdminArea = false;
    /** @var bool */
    private $suppressCheckQtyIncrements = false;
    /** @var bool */
    private $isSaleable = true;
    /** @var int */
    private $orderedItems = 0;
    /** @var string */
    private $productName = '';
    /** @var bool */
    private $isChildItem = false;
    /** @var bool */
    private $hasStockQty = false;
    /** @var float|null */
    private $stockQty = null;
    /** @var bool */
    private $hasIsChildItem = false;

    /**
     * Mapping from test format (method names) to parent format (constant keys)
     *
     * @var array
     */
    private static $keyMapping = [
        'getProductId' => self::PRODUCT_ID,
        'getStockId' => self::STOCK_ID,
        'getQty' => self::QTY,
        'getIsInStock' => self::IS_IN_STOCK,
        'getIsQtyDecimal' => self::IS_QTY_DECIMAL,
        'getShowDefaultNotificationMessage' => self::SHOW_DEFAULT_NOTIFICATION_MESSAGE,
        'getUseConfigMinQty' => self::USE_CONFIG_MIN_QTY,
        'getMinQty' => self::MIN_QTY,
        'getUseConfigMinSaleQty' => self::USE_CONFIG_MIN_SALE_QTY,
        'getMinSaleQty' => self::MIN_SALE_QTY,
        'getUseConfigMaxSaleQty' => self::USE_CONFIG_MAX_SALE_QTY,
        'getMaxSaleQty' => self::MAX_SALE_QTY,
        'getUseConfigBackorders' => self::USE_CONFIG_BACKORDERS,
        'getBackorders' => self::BACKORDERS,
        'getUseConfigNotifyStockQty' => self::USE_CONFIG_NOTIFY_STOCK_QTY,
        'getNotifyStockQty' => self::NOTIFY_STOCK_QTY,
        'getUseConfigQtyIncrements' => self::USE_CONFIG_QTY_INCREMENTS,
        'getQtyIncrements' => self::QTY_INCREMENTS,
        'getUseConfigEnableQtyInc' => self::USE_CONFIG_ENABLE_QTY_INC,
        'getEnableQtyIncrements' => self::ENABLE_QTY_INCREMENTS,
        'getUseConfigManageStock' => self::USE_CONFIG_MANAGE_STOCK,
        'getManageStock' => self::MANAGE_STOCK,
        'getLowStockDate' => self::LOW_STOCK_DATE,
        'getIsDecimalDivided' => self::IS_DECIMAL_DIVIDED,
        'getStockStatusChangedAuto' => self::STOCK_STATUS_CHANGED_AUTO,
    ];

    /**
     * Skip parent constructor to avoid dependency injection
     */
    public function __construct()
    {
        $this->_data = [];
    }

    /**
     * Override setData to automatically convert test format keys to parent format
     *
     * @param string|array $key
     * @param mixed $value
     * @return $this
     */
    public function setData($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->setData($k, $v);
            }
            return $this;
        }

        // Convert method name keys to constant keys
        if (isset(self::$keyMapping[$key])) {
            $key = self::$keyMapping[$key];
        }

        return parent::setData($key, $value);
    }

    /**
     * Override getWebsiteId to avoid null stockConfiguration dependency
     *
     * @return int
     */
    public function getWebsiteId()
    {
        return (int)$this->getData(self::WEBSITE_ID);
    }

    /**
     * Override getStockId to avoid null stockRegistry dependency
     *
     * @return int
     */
    public function getStockId()
    {
        return (int)$this->getData(self::STOCK_ID);
    }

    /**
     * Override getIsInStock to avoid getManageStock() dependency
     *
     * @return bool|int
     */
    public function getIsInStock()
    {
        return (bool)$this->getData(self::IS_IN_STOCK);
    }

    /**
     * Override getManageStock to avoid null stockConfiguration dependency
     *
     * @return int
     */
    public function getManageStock()
    {
        return (int)$this->getData(self::MANAGE_STOCK);
    }

    /**
     * Override getQtyIncrements - parent has complex caching logic with $this->qtyIncrements property
     *
     * @return float|false
     */
    public function getQtyIncrements()
    {
        $value = $this->getData(self::QTY_INCREMENTS);
        if ($value !== null && $value > 0) {
            return $this->getIsQtyDecimal() ? (float) $value : (int) $value;
        }

        if ($this->getEnableQtyIncrements()) {
            if ($this->getUseConfigQtyIncrements()) {
                return 0.0;
            }
            return (float) $value;
        }
        return false;
    }

    /**
     * Set stock status changed automatically flag (test stub)
     *
     * @param mixed $flag
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setStockStatusChangedAutomaticallyFlag($flag)
    {
        return $this;
    }

    /**
     * Get stock status changed automatically flag (test stub)
     *
     * @return bool
     */
    public function getStockStatusChangedAutomaticallyFlag()
    {
        return false;
    }

    /**
     * Has admin area
     *
     * @return bool
     */
    public function hasAdminArea()
    {
        return $this->hasAdminArea;
    }

    /**
     * Set has admin area
     *
     * @param bool $value
     * @return $this
     */
    public function setHasAdminArea($value)
    {
        $this->hasAdminArea = $value;
        return $this;
    }

    /**
     * Get suppress check qty increments
     *
     * @return bool
     */
    public function getSuppressCheckQtyIncrements()
    {
        return $this->suppressCheckQtyIncrements;
    }

    /**
     * Set suppress check qty increments
     *
     * @param bool $value
     * @return $this
     */
    public function setSuppressCheckQtyIncrements($value)
    {
        $this->suppressCheckQtyIncrements = $value;
        return $this;
    }

    /**
     * Get is saleable
     *
     * @return bool
     */
    public function getIsSaleable()
    {
        return $this->isSaleable;
    }

    /**
     * Set is saleable
     *
     * @param bool $value
     * @return $this
     */
    public function setIsSaleable($value)
    {
        $this->isSaleable = $value;
        return $this;
    }

    /**
     * Get ordered items
     *
     * @return int
     */
    public function getOrderedItems()
    {
        return $this->orderedItems;
    }

    /**
     * Set ordered items
     *
     * @param int $value
     * @return $this
     */
    public function setOrderedItems($value)
    {
        $this->orderedItems = $value;
        return $this;
    }

    /**
     * Get product name
     *
     * @return string
     */
    public function getProductName()
    {
        return $this->productName;
    }

    /**
     * Set product name
     *
     * @param string $value
     * @return $this
     */
    public function setProductName($value)
    {
        $this->productName = $value;
        return $this;
    }

    /**
     * Get is child item
     *
     * @return bool
     */
    public function getIsChildItem()
    {
        return $this->isChildItem;
    }

    /**
     * Set is child item
     *
     * @param bool $value
     * @return $this
     */
    public function setIsChildItem($value)
    {
        $this->isChildItem = $value;
        return $this;
    }

    /**
     * Has stock qty
     *
     * @return bool
     */
    public function hasStockQty()
    {
        return $this->hasStockQty;
    }

    /**
     * Set has stock qty
     *
     * @param bool $value
     * @return $this
     */
    public function setHasStockQty($value)
    {
        $this->hasStockQty = $value;
        return $this;
    }

    /**
     * Set stock qty
     *
     * @param float|null $value
     * @return $this
     */
    public function setStockQty($value)
    {
        $this->stockQty = $value;
        $this->_data['stock_qty'] = $value;
        return $this;
    }

    /**
     * Get stock qty
     *
     * @return float|null
     */
    public function getStockQty()
    {
        // Check _data first (for consistency with StockStateProvider), then property
        if (isset($this->_data['stock_qty'])) {
            return $this->_data['stock_qty'];
        }
        return $this->stockQty;
    }

    /**
     * Check quote item qty
     *
     * @param mixed $qty
     * @param mixed $summaryQty
     * @param mixed $origQty
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function checkQuoteItemQty($qty, $summaryQty, $origQty = null)
    {
        return null;
    }

    /**
     * Has is child item
     *
     * @return bool
     */
    public function hasIsChildItem()
    {
        return $this->hasIsChildItem;
    }

    /**
     * Set has is child item
     *
     * @param bool $hasIsChildItem
     * @return $this
     */
    public function setHasIsChildItem($hasIsChildItem)
    {
        $this->hasIsChildItem = $hasIsChildItem;
        return $this;
    }

    /**
     * Uns is child item
     *
     * @return $this
     */
    public function unsIsChildItem()
    {
        $this->isChildItem = false;
        return $this;
    }
}
