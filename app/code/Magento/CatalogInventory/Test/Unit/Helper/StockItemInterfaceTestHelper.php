<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogInventory\Test\Unit\Helper;

use Magento\CatalogInventory\Api\Data\StockItemInterface;

/**
 * TestHelper for StockItemInterface
 * Provides implementation for StockItemInterface with DataObject methods
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ComplexMethod)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.BooleanGetMethodName)
 */
class StockItemInterfaceTestHelper implements StockItemInterface
{
    /** @var array */
    private $data = [];
    /** @var int|null */
    private $itemId = null;
    /** @var int|null */
    private $productId = null;
    /** @var int|null */
    private $stockId = null;
    /** @var float|null */
    private $qty = null;
    /** @var bool|null */
    private $isInStock = null;
    /** @var bool|null */
    private $isQtyDecimal = null;
    /** @var bool|null */
    private $showDefaultNotificationMessage = null;
    /** @var bool|null */
    private $useConfigMinQty = null;
    /** @var float|null */
    private $minQty = null;
    /** @var bool|null */
    private $useConfigMinSaleQty = null;
    /** @var float|null */
    private $minSaleQty = null;
    /** @var bool|null */
    private $useConfigMaxSaleQty = null;
    /** @var float|null */
    private $maxSaleQty = null;
    /** @var bool|null */
    private $useConfigBackorders = null;
    /** @var int|null */
    private $backorders = null;
    /** @var bool|null */
    private $useConfigNotifyStockQty = null;
    /** @var float|null */
    private $notifyStockQty = null;
    /** @var bool|null */
    private $useConfigQtyIncrements = null;
    /** @var float|null */
    private $qtyIncrements = null;
    /** @var bool|null */
    private $useConfigEnableQtyInc = null;
    /** @var bool|null */
    private $enableQtyIncrements = null;
    /** @var bool|null */
    private $useConfigManageStock = null;
    /** @var bool|null */
    private $manageStock = null;
    /** @var string|null */
    private $lowStockDate = null;
    /** @var bool|null */
    private $isDecimalDivided = null;
    /** @var bool|null */
    private $stockStatusChangedAuto = null;
    /** @var mixed */
    private $extensionAttributes = null;
    /** @var int|null */
    private $websiteId = null;
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

    public function __construct()
    {
    }

    // DataObject methods
    public function getData($key = '', $index = null)
    {
        if ('' === $key) {
            return $this->data;
        }
        return $this->data[$key] ?? null;
    }

    public function setData($key, $value = null)
    {
        if (is_array($key)) {
            $this->data = $key;
        } else {
            $this->data[$key] = $value;
        }
        return $this;
    }

    public function addData(array $arr)
    {
        foreach ($arr as $index => $value) {
            $this->setData($index, $value);
        }
        return $this;
    }

    public function unsetData($key = null)
    {
        if ($key === null) {
            $this->data = [];
        } elseif (is_string($key)) {
            unset($this->data[$key]);
        }
        return $this;
    }

    public function hasData($key = '')
    {
        return isset($this->data[$key]);
    }

    public function toArray(array $keys = [])
    {
        return $this->data;
    }

    public function toJson(array $keys = [])
    {
        return json_encode($this->data);
    }

    public function toString($format = '')
    {
        return json_encode($this->data);
    }

    public function isEmpty()
    {
        return empty($this->data);
    }

    // StockItemInterface methods
    public function getItemId()
    {
        return $this->itemId;
    }

    public function setItemId($itemId)
    {
        $this->itemId = $itemId;
        return $this;
    }

    public function getProductId()
    {
        return $this->productId;
    }

    public function setProductId($productId)
    {
        $this->productId = $productId;
        return $this;
    }

    public function getStockId()
    {
        return $this->stockId;
    }

    public function setStockId($stockId)
    {
        $this->stockId = $stockId;
        return $this;
    }

    public function getQty()
    {
        // Check data first (for dynamic setting), then property
        if (isset($this->data['getQty'])) {
            return $this->data['getQty'];
        }
        return $this->qty;
    }

    public function setQty($qty)
    {
        $this->qty = $qty;
        return $this;
    }

    public function getIsInStock()
    {
        // Check data first (for dynamic setting), then property
        if (isset($this->data['getIsInStock'])) {
            return $this->data['getIsInStock'];
        }
        return $this->isInStock;
    }

    public function setIsInStock($isInStock)
    {
        $this->isInStock = $isInStock;
        return $this;
    }

    public function getIsQtyDecimal()
    {
        return $this->isQtyDecimal;
    }

    public function setIsQtyDecimal($isQtyDecimal)
    {
        $this->isQtyDecimal = $isQtyDecimal;
        return $this;
    }

    public function getShowDefaultNotificationMessage()
    {
        return $this->showDefaultNotificationMessage;
    }

    public function getUseConfigMinQty()
    {
        return $this->useConfigMinQty;
    }

    public function setUseConfigMinQty($useConfigMinQty)
    {
        $this->useConfigMinQty = $useConfigMinQty;
        return $this;
    }

    public function getMinQty()
    {
        // Check data first (for dynamic setting), then property
        if (isset($this->data['getMinQty'])) {
            return $this->data['getMinQty'];
        }
        return $this->minQty;
    }

    public function setMinQty($minQty)
    {
        $this->minQty = $minQty;
        return $this;
    }

    public function getUseConfigMinSaleQty()
    {
        return $this->useConfigMinSaleQty;
    }

    public function setUseConfigMinSaleQty($useConfigMinSaleQty)
    {
        $this->useConfigMinSaleQty = $useConfigMinSaleQty;
        return $this;
    }

    public function getMinSaleQty()
    {
        // Check data first (for dynamic setting), then property
        if (isset($this->data['getMinSaleQty'])) {
            return $this->data['getMinSaleQty'];
        }
        return $this->minSaleQty;
    }

    public function setMinSaleQty($minSaleQty)
    {
        $this->minSaleQty = $minSaleQty;
        return $this;
    }

    public function getUseConfigMaxSaleQty()
    {
        return $this->useConfigMaxSaleQty;
    }

    public function setUseConfigMaxSaleQty($useConfigMaxSaleQty)
    {
        $this->useConfigMaxSaleQty = $useConfigMaxSaleQty;
        return $this;
    }

    public function getMaxSaleQty()
    {
        // Check data first (for dynamic setting), then property
        if (isset($this->data['getMaxSaleQty'])) {
            return $this->data['getMaxSaleQty'];
        }
        return $this->maxSaleQty;
    }

    public function setMaxSaleQty($maxSaleQty)
    {
        $this->maxSaleQty = $maxSaleQty;
        return $this;
    }

    public function getUseConfigBackorders()
    {
        return $this->useConfigBackorders;
    }

    public function setUseConfigBackorders($useConfigBackorders)
    {
        $this->useConfigBackorders = $useConfigBackorders;
        return $this;
    }

    public function getBackorders()
    {
        // Check data first (for dynamic setting), then property
        if (isset($this->data['getBackorders'])) {
            return $this->data['getBackorders'];
        }
        return $this->backorders;
    }

    public function setBackorders($backOrders)
    {
        $this->backorders = $backOrders;
        return $this;
    }

    public function getUseConfigNotifyStockQty()
    {
        return $this->useConfigNotifyStockQty;
    }

    public function setUseConfigNotifyStockQty($useConfigNotifyStockQty)
    {
        $this->useConfigNotifyStockQty = $useConfigNotifyStockQty;
        return $this;
    }

    public function getNotifyStockQty()
    {
        // Check data first (for dynamic setting), then property
        if (isset($this->data['getNotifyStockQty'])) {
            return $this->data['getNotifyStockQty'];
        }
        return $this->notifyStockQty;
    }

    public function setNotifyStockQty($notifyStockQty)
    {
        $this->notifyStockQty = $notifyStockQty;
        return $this;
    }

    public function getUseConfigQtyIncrements()
    {
        return $this->useConfigQtyIncrements;
    }

    public function setUseConfigQtyIncrements($useConfigQtyIncrements)
    {
        $this->useConfigQtyIncrements = $useConfigQtyIncrements;
        return $this;
    }

    public function getQtyIncrements()
    {
        // Check data first (for dynamic setting), then property
        if (isset($this->data['getQtyIncrements'])) {
            return $this->data['getQtyIncrements'];
        }
        return $this->qtyIncrements;
    }

    public function setQtyIncrements($qtyIncrements)
    {
        $this->qtyIncrements = $qtyIncrements;
        return $this;
    }

    public function getUseConfigEnableQtyInc()
    {
        return $this->useConfigEnableQtyInc;
    }

    public function setUseConfigEnableQtyInc($useConfigEnableQtyInc)
    {
        $this->useConfigEnableQtyInc = $useConfigEnableQtyInc;
        return $this;
    }

    public function getEnableQtyIncrements()
    {
        return $this->enableQtyIncrements;
    }

    public function setEnableQtyIncrements($enableQtyIncrements)
    {
        $this->enableQtyIncrements = $enableQtyIncrements;
        return $this;
    }

    public function getUseConfigManageStock()
    {
        return $this->useConfigManageStock;
    }

    public function setUseConfigManageStock($useConfigManageStock)
    {
        $this->useConfigManageStock = $useConfigManageStock;
        return $this;
    }

    public function getManageStock()
    {
        // Check data first (for dynamic setting), then property
        if (isset($this->data['getManageStock'])) {
            return $this->data['getManageStock'];
        }
        return $this->manageStock;
    }

    public function setManageStock($manageStock)
    {
        $this->manageStock = $manageStock;
        return $this;
    }

    public function getLowStockDate()
    {
        return $this->lowStockDate;
    }

    public function setLowStockDate($lowStockDate)
    {
        $this->lowStockDate = $lowStockDate;
        return $this;
    }

    public function getIsDecimalDivided()
    {
        return $this->isDecimalDivided;
    }

    public function setIsDecimalDivided($isDecimalDivided)
    {
        $this->isDecimalDivided = $isDecimalDivided;
        return $this;
    }

    public function getStockStatusChangedAuto()
    {
        return $this->stockStatusChangedAuto;
    }

    public function setStockStatusChangedAuto($stockStatusChangedAuto)
    {
        $this->stockStatusChangedAuto = $stockStatusChangedAuto;
        return $this;
    }

    public function getExtensionAttributes()
    {
        return $this->extensionAttributes;
    }

    public function setExtensionAttributes($extensionAttributes)
    {
        $this->extensionAttributes = $extensionAttributes;
        return $this;
    }

    public function getWebsiteId()
    {
        return $this->websiteId;
    }

    public function setWebsiteId($websiteId)
    {
        $this->websiteId = $websiteId;
        return $this;
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
        $this->data['stock_qty'] = $value;
        return $this;
    }

    /**
     * Get stock qty
     *
     * @return float|null
     */
    public function getStockQty()
    {
        // Check data first (for consistency with StockStateProvider), then property
        if (isset($this->data['stock_qty'])) {
            return $this->data['stock_qty'];
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

