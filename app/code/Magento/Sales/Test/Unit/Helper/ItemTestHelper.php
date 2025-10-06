<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Sales\Test\Unit\Helper;

use Magento\Sales\Model\Order\Item;

/**
 * Test helper class for Order Item with custom methods
 * 
 * This helper is placed in Magento_Sales module as it's the core module
 * that contains the Order\Item class and is used by many other modules
 * including Bundle, Weee, Tax, SalesGraphQl, etc.
 */
class ItemTestHelper extends Item
{
    private $data = [];

    /**
     * Skip parent constructor to avoid dependencies
     */
    public function __construct()
    {
        // Skip parent constructor
    }

    /**
     * Custom method for testing - returns self to simulate order item behavior
     *
     * @return self
     */
    public function getOrderItem(): self
    {
        return $this->data['order_item'] ?? $this;
    }

    /**
     * Set order item for testing
     *
     * @param mixed $item
     * @return self
     */
    public function setOrderItem($item): self
    {
        $this->data['order_item'] = $item;
        return $this;
    }

    /**
     * Custom method for testing - returns order item ID
     *
     * @return int|null
     */
    public function getOrderItemId(): ?int
    {
        return $this->data['order_item_id'] ?? null;
    }

    /**
     * Set order item ID for testing
     *
     * @param int|null $id
     * @return self
     */
    public function setOrderItemId(?int $id): self
    {
        $this->data['order_item_id'] = $id;
        return $this;
    }

    /**
     * Override getProductOptions for testing
     *
     * @return array
     */
    public function getProductOptions(): array
    {
        return $this->data['product_options'] ?? [];
    }

    /**
     * Set product options for testing
     *
     * @param array|null $options
     * @return self
     */
    public function setProductOptions(?array $options = null): self
    {
        $this->data['product_options'] = $options;
        return $this;
    }

    /**
     * Override getParentItem for testing
     *
     * @return Item|null
     */
    public function getParentItem(): ?Item
    {
        $parentItem = $this->data['parent_item'] ?? null;
        
        // Handle boolean values from test data providers
        if ($parentItem === true) {
            // Return a mock Item when true is passed
            return $this;
        }
        if ($parentItem === false) {
            // Return null when false is passed
            return null;
        }
        
        return $parentItem;
    }

    /**
     * Set parent item for testing
     *
     * @param mixed $item
     * @return self
     */
    public function setParentItem($item): self
    {
        $this->data['parent_item'] = $item;
        return $this;
    }

    /**
     * Generic data setter for flexible testing
     *
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function setTestData(string $key, $value): self
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * Generic data getter for flexible testing
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getTestData(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * Override getId for testing
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->data['id'] ?? null;
    }

    /**
     * Set ID for testing
     *
     * @param mixed $value
     * @return self
     */
    public function setId($value): self
    {
        $this->data['id'] = $value;
        return $this;
    }

    /**
     * Custom getHasChildren method for Bundle testing
     *
     * @return bool
     */
    public function getHasChildren(): bool
    {
        return $this->data['has_children'] ?? false;
    }

    /**
     * Set has children for testing
     *
     * @param bool $hasChildren
     * @return self
     */
    public function setHasChildren(bool $hasChildren): self
    {
        $this->data['has_children'] = $hasChildren;
        return $this;
    }

    /**
     * Custom getProductType method for testing
     *
     * @return string|null
     */
    public function getProductType(): ?string
    {
        return $this->data['product_type'] ?? null;
    }

    /**
     * Set product type for testing
     *
     * @param mixed $productType
     * @return self
     */
    public function setProductType($productType): self
    {
        $this->data['product_type'] = $productType;
        return $this;
    }

    /**
     * Custom getProduct method for testing
     *
     * @return mixed
     */
    public function getProduct()
    {
        return $this->data['product'] ?? null;
    }

    /**
     * Set product for testing
     *
     * @param mixed $product
     * @return self
     */
    public function setProduct($product): self
    {
        $this->data['product'] = $product;
        return $this;
    }

    /**
     * Custom getSku method for testing
     *
     * @return string|null
     */
    public function getSku(): ?string
    {
        return $this->data['sku'] ?? null;
    }

    /**
     * Set SKU for testing
     *
     * @param mixed $sku
     * @return self
     */
    public function setSku($sku): self
    {
        $this->data['sku'] = $sku;
        return $this;
    }

    /**
     * Add data to the item (like DataObject::addData)
     *
     * @param array $data
     * @return self
     */
    public function addData(array $data): self
    {
        $this->data = array_merge($this->data, $data);
        return $this;
    }

    /**
     * Add child item for testing
     *
     * @param mixed $childItem
     * @return self
     */
    public function addChildItem($childItem): self
    {
        if (!isset($this->data['child_items'])) {
            $this->data['child_items'] = [];
        }
        $this->data['child_items'][] = $childItem;
        return $this;
    }

    /**
     * Get child items for testing
     *
     * @return array
     */
    public function getChildrenItems(): array
    {
        return $this->data['child_items'] ?? [];
    }
}
