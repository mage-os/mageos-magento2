<?php
/**
 * Copyright 2016 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogInventory\Test\Unit\Helper;

use Magento\CatalogInventory\Api\StockConfigurationInterface;

/**
 * TestHelper for StockConfigurationInterface
 * Provides implementation for StockConfigurationInterface with additional test methods
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class StockConfigurationInterfaceTestHelper implements StockConfigurationInterface
{
    /** @var float|null */
    private $stockThresholdQty = null;
    /** @var array */
    private $data = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid complex dependencies
    }

    /**
     * Get stock threshold qty
     *
     * @return float|null
     */
    public function getStockThresholdQty()
    {
        return $this->stockThresholdQty;
    }

    /**
     * Set stock threshold qty
     *
     * @param float|null $stockThresholdQty
     * @return $this
     */
    public function setStockThresholdQty($stockThresholdQty)
    {
        $this->stockThresholdQty = $stockThresholdQty;
        return $this;
    }

    /**
     * Get default scope id
     *
     * @return int|null
     */
    public function getDefaultScopeId()
    {
        return $this->data['default_scope_id'] ?? null;
    }

    /**
     * Set default scope id
     *
     * @param int|null $scopeId
     * @return $this
     */
    public function setDefaultScopeId($scopeId)
    {
        $this->data['default_scope_id'] = $scopeId;
        return $this;
    }

    /**
     * Get default config value
     *
     * @param string $field
     * @param int|null $storeId
     * @return mixed
     */
    public function getDefaultConfigValue($field, $storeId = null)
    {
        return $this->data[$field] ?? null;
    }

    /**
     * Set default config value
     *
     * @param string $field
     * @param mixed $value
     * @return $this
     */
    public function setDefaultConfigValue($field, $value)
    {
        $this->data[$field] = $value;
        return $this;
    }

    /**
     * Get manage stock
     *
     * @param int|null $storeId
     * @return int|null
     */
    public function getManageStock($storeId = null)
    {
        return $this->data['manage_stock'] ?? null;
    }

    /**
     * Set manage stock
     *
     * @param int|null $manageStock
     * @return $this
     */
    public function setManageStock($manageStock)
    {
        $this->data['manage_stock'] = $manageStock;
        return $this;
    }

    /**
     * Get backorders
     *
     * @param int|null $storeId
     * @return int|null
     */
    public function getBackorders($storeId = null)
    {
        return $this->data['backorders'] ?? null;
    }

    /**
     * Set backorders
     *
     * @param int|null $backorders
     * @return $this
     */
    public function setBackorders($backorders)
    {
        $this->data['backorders'] = $backorders;
        return $this;
    }

    /**
     * Get min qty
     *
     * @param int|null $storeId
     * @return float|null
     */
    public function getMinQty($storeId = null)
    {
        return $this->data['min_qty'] ?? null;
    }

    /**
     * Set min qty
     *
     * @param float|null $minQty
     * @return $this
     */
    public function setMinQty($minQty)
    {
        $this->data['min_qty'] = $minQty;
        return $this;
    }

    /**
     * Get min sale qty
     *
     * @param int|null $storeId
     * @param int|null $customerGroupId
     * @return float|null
     */
    public function getMinSaleQty($storeId = null, $customerGroupId = null)
    {
        return $this->data['min_sale_qty'] ?? null;
    }

    /**
     * Set min sale qty
     *
     * @param float|null $minSaleQty
     * @return $this
     */
    public function setMinSaleQty($minSaleQty)
    {
        $this->data['min_sale_qty'] = $minSaleQty;
        return $this;
    }

    /**
     * Get max sale qty
     *
     * @param int|null $storeId
     * @return float|null
     */
    public function getMaxSaleQty($storeId = null)
    {
        return $this->data['max_sale_qty'] ?? null;
    }

    /**
     * Set max sale qty
     *
     * @param float|null $maxSaleQty
     * @return $this
     */
    public function setMaxSaleQty($maxSaleQty)
    {
        $this->data['max_sale_qty'] = $maxSaleQty;
        return $this;
    }

    /**
     * Get notify stock qty
     *
     * @param int|null $storeId
     * @return float|null
     */
    public function getNotifyStockQty($storeId = null)
    {
        return $this->data['notify_stock_qty'] ?? null;
    }

    /**
     * Set notify stock qty
     *
     * @param float|null $notifyStockQty
     * @return $this
     */
    public function setNotifyStockQty($notifyStockQty)
    {
        $this->data['notify_stock_qty'] = $notifyStockQty;
        return $this;
    }

    /**
     * Get enable qty increments
     *
     * @param int|null $storeId
     * @return bool|null
     */
    public function getEnableQtyIncrements($storeId = null)
    {
        return $this->data['enable_qty_increments'] ?? null;
    }

    /**
     * Set enable qty increments
     *
     * @param bool|null $enableQtyIncrements
     * @return $this
     */
    public function setEnableQtyIncrements($enableQtyIncrements)
    {
        $this->data['enable_qty_increments'] = $enableQtyIncrements;
        return $this;
    }

    /**
     * Get qty increments
     *
     * @param mixed $store
     * @return float|null
     */
    public function getQtyIncrements($store = null)
    {
        return $this->data['qty_increments'] ?? null;
    }

    /**
     * Set qty increments
     *
     * @param float|null $qtyIncrements
     * @return $this
     */
    public function setQtyIncrements($qtyIncrements)
    {
        $this->data['qty_increments'] = $qtyIncrements;
        return $this;
    }

    /**
     * Check if show out of stock
     *
     * @param int|null $storeId
     * @return bool|null
     */
    public function isShowOutOfStock($storeId = null)
    {
        return $this->data['show_out_of_stock'] ?? null;
    }

    /**
     * Set show out of stock
     *
     * @param bool|null $showOutOfStock
     * @return $this
     */
    public function setShowOutOfStock($showOutOfStock)
    {
        $this->data['show_out_of_stock'] = $showOutOfStock;
        return $this;
    }

    /**
     * Check if auto return enabled
     *
     * @param int|null $storeId
     * @return bool|null
     */
    public function isAutoReturnEnabled($storeId = null)
    {
        return $this->data['auto_return_enabled'] ?? null;
    }

    /**
     * Set auto return enabled
     *
     * @param bool|null $autoReturnEnabled
     * @return $this
     */
    public function setAutoReturnEnabled($autoReturnEnabled)
    {
        $this->data['auto_return_enabled'] = $autoReturnEnabled;
        return $this;
    }

    /**
     * Check if display product stock status
     *
     * @param int|null $storeId
     * @return bool|null
     */
    public function isDisplayProductStockStatus($storeId = null)
    {
        return $this->data['display_product_stock_status'] ?? null;
    }

    /**
     * Set display product stock status
     *
     * @param bool|null $displayProductStockStatus
     * @return $this
     */
    public function setDisplayProductStockStatus($displayProductStockStatus)
    {
        $this->data['display_product_stock_status'] = $displayProductStockStatus;
        return $this;
    }

    /**
     * Get item options
     *
     * @return array|null
     */
    public function getItemOptions()
    {
        return $this->data['item_options'] ?? null;
    }

    /**
     * Set item options
     *
     * @param array|null $itemOptions
     * @return $this
     */
    public function setItemOptions($itemOptions)
    {
        $this->data['item_options'] = $itemOptions;
        return $this;
    }

    /**
     * Get is qty type ids
     *
     * @param string|null $filter
     * @return array|null
     */
    public function getIsQtyTypeIds($filter = null)
    {
        return $this->data['is_qty_type_ids'] ?? null;
    }

    /**
     * Set is qty type ids
     *
     * @param array|null $isQtyTypeIds
     * @return $this
     */
    public function setIsQtyTypeIds($isQtyTypeIds)
    {
        $this->data['is_qty_type_ids'] = $isQtyTypeIds;
        return $this;
    }

    /**
     * Check if qty
     *
     * @param string $productTypeId
     * @return bool|null
     */
    public function isQty($productTypeId)
    {
        return $this->data['is_qty_' . $productTypeId] ?? null;
    }

    /**
     * Set is qty
     *
     * @param string $productTypeId
     * @param bool|null $isQty
     * @return $this
     */
    public function setIsQty($productTypeId, $isQty)
    {
        $this->data['is_qty_' . $productTypeId] = $isQty;
        return $this;
    }

    /**
     * Check if can subtract qty
     *
     * @param int|null $storeId
     * @return bool|null
     */
    public function canSubtractQty($storeId = null)
    {
        return $this->data['can_subtract_qty'] ?? null;
    }

    /**
     * Set can subtract qty
     *
     * @param bool|null $canSubtractQty
     * @return $this
     */
    public function setCanSubtractQty($canSubtractQty)
    {
        $this->data['can_subtract_qty'] = $canSubtractQty;
        return $this;
    }

    /**
     * Get can back in stock
     *
     * @param int|null $storeId
     * @return bool|null
     */
    public function getCanBackInStock($storeId = null)
    {
        return $this->data['can_back_in_stock'] ?? null;
    }

    /**
     * Set can back in stock
     *
     * @param bool|null $canBackInStock
     * @return $this
     */
    public function setCanBackInStock($canBackInStock)
    {
        $this->data['can_back_in_stock'] = $canBackInStock;
        return $this;
    }

    /**
     * Get config item options
     *
     * @return array|null
     */
    public function getConfigItemOptions()
    {
        return $this->data['config_item_options'] ?? null;
    }

    /**
     * Set config item options
     *
     * @param array|null $configItemOptions
     * @return $this
     */
    public function setConfigItemOptions($configItemOptions)
    {
        $this->data['config_item_options'] = $configItemOptions;
        return $this;
    }
}
