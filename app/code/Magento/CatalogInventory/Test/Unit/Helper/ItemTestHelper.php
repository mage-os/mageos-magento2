<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogInventory\Test\Unit\Helper;

use Magento\CatalogInventory\Model\Stock\Item as StockItem;

/**
 * Test helper class for StockItem with custom methods
 *
 * This helper extends the StockItem class to provide custom methods
 * needed for testing that don't exist in the parent class.
 */
class ItemTestHelper extends StockItem
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * Skip parent constructor to avoid dependencies
     */
    public function __construct()
    {
        // Skip parent constructor
    }

    /**
     * Custom method for UpdateStockChangedAuto tests
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsInStock()
    {
        return $this->data['is_in_stock'] ?? true;
    }

    /**
     * Custom method for UpdateStockChangedAuto tests
     *
     * @param mixed $isInStock
     * @return self
     */
    public function setIsInStock($isInStock)
    {
        $this->data['is_in_stock'] = $isInStock;
        return $this;
    }

    /**
     * Custom method for UpdateStockChangedAuto tests
     *
     * @param mixed $auto
     * @return self
     */
    public function setStockStatusChangedAuto($auto)
    {
        $this->data['stock_status_changed_auto'] = $auto;
        return $this;
    }

    /**
     * Custom method for UpdateStockChangedAuto tests
     *
     * @return mixed
     */
    public function getProductId()
    {
        return $this->data['product_id'] ?? null;
    }

    /**
     * Custom method for UpdateStockChangedAuto tests
     *
     * @param mixed $productId
     * @return self
     */
    public function setProductId($productId): self
    {
        $this->data['product_id'] = $productId;
        return $this;
    }

    /**
     * Custom method for UpdateStockChangedAuto tests
     *
     * @return bool
     */
    public function hasStockStatusChangedAutomaticallyFlag()
    {
        return $this->data['has_stock_status_changed_automatically_flag'] ?? false;
    }

    /**
     * Custom method for UpdateStockChangedAuto tests
     *
     * @param mixed $hasFlag
     * @return self
     */
    public function setHasStockStatusChangedAutomaticallyFlag($hasFlag)
    {
        $this->data['has_stock_status_changed_automatically_flag'] = $hasFlag;
        return $this;
    }

    /**
     * Override getData to work with our data array
     *
     * @param string $key
     * @param mixed $index
     * @return mixed
     */
    public function getData($key = '', $index = null)
    {
        if ($key === '') {
            return $this->data;
        }

        if ($index !== null) {
            return $this->data[$key][$index] ?? null;
        }

        return $this->data[$key] ?? null;
    }

    /**
     * Override setData to work with our data array
     *
     * @param string|array $key
     * @param mixed $value
     * @return self
     */
    public function setData($key, $value = null)
    {
        if (is_array($key)) {
            $this->data = array_merge($this->data, $key);
        } else {
            $this->data[$key] = $value;
        }
        return $this;
    }

    /**
     * Get product name
     *
     * @return string|null
     */
    public function getProductName(): ?string
    {
        return $this->data['product_name'] ?? null;
    }

    /**
     * Set product name
     *
     * @param string $name
     * @return self
     */
    public function setProductName($name): self
    {
        $this->data['product_name'] = $name;
        return $this;
    }
}
