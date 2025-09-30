<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Bundle\Test\Unit\Helper;

use Magento\Bundle\Model\Product\Type;

/**
 * Test helper for Magento\Bundle\Model\Product\Type
 *
 * Extends the Type class to add custom methods for testing
 */
class TypeTestHelper extends Type
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependencies
    }

    /**
     * Custom setParentProductId method for testing
     *
     * @param mixed $productId
     * @return self
     */
    public function setParentProductId($productId): self
    {
        $this->data['parent_product_id'] = $productId;
        return $this;
    }

    /**
     * Custom getParentProductId method for testing
     *
     * @return mixed
     */
    public function getParentProductId()
    {
        return $this->data['parent_product_id'] ?? null;
    }

    /**
     * Custom addCustomOption method for testing
     *
     * @param mixed $option
     * @return self
     */
    public function addCustomOption($option): self
    {
        if (!isset($this->data['custom_options'])) {
            $this->data['custom_options'] = [];
        }
        $this->data['custom_options'][] = $option;
        return $this;
    }

    /**
     * Custom getCustomOptions method for testing
     *
     * @return array
     */
    public function getCustomOptions(): array
    {
        return $this->data['custom_options'] ?? [];
    }

    /**
     * Custom setCartQty method for testing
     *
     * @param mixed $qty
     * @return self
     */
    public function setCartQty($qty): self
    {
        $this->data['cart_qty'] = $qty;
        return $this;
    }

    /**
     * Custom getCartQty method for testing
     *
     * @return mixed
     */
    public function getCartQty()
    {
        return $this->data['cart_qty'] ?? null;
    }

    /**
     * Custom getSelectionId method for testing
     *
     * @return mixed
     */
    public function getSelectionId()
    {
        return $this->data['selection_id'] ?? null;
    }

    /**
     * Custom setSelectionId method for testing
     *
     * @param mixed $selectionId
     * @return self
     */
    public function setSelectionId($selectionId): self
    {
        $this->data['selection_id'] = $selectionId;
        return $this;
    }

    /**
     * Set test data for flexible state management
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
     * Get test data
     *
     * @param string $key
     * @return mixed
     */
    public function getTestData(string $key)
    {
        return $this->data[$key] ?? null;
    }

    /**
     * Custom setStoreFilter method for testing
     *
     * @param mixed $store
     * @param mixed $product
     * @return self
     */
    public function setStoreFilter($store = null, $product = null): self
    {
        $this->data['store_filter'] = $store;
        return $this;
    }

    /**
     * Custom getStoreFilter method for testing
     *
     * @param mixed $product
     * @return mixed
     */
    public function getStoreFilter($product = null)
    {
        return $this->data['store_filter'] ?? null;
    }

    /**
     * Custom prepareForCart method for testing
     *
     * @param mixed $buyRequest
     * @param mixed $product
     * @param mixed $processMode
     * @return mixed
     */
    public function prepareForCart($buyRequest, $product, $processMode = null)
    {
        return $this->data['prepare_for_cart'] ?? [$this];
    }

    /**
     * Set prepareForCart return value for testing
     *
     * @param mixed $value
     * @return self
     */
    public function setPrepareForCart($value): self
    {
        $this->data['prepare_for_cart'] = $value;
        return $this;
    }

    /**
     * Get options collection for testing
     *
     * @param mixed $product
     * @return mixed
     */
    public function getOptionsCollection($product = null)
    {
        return $this->data['options_collection'] ?? null;
    }

    /**
     * Set options collection for testing
     *
     * @param mixed $collection
     * @return self
     */
    public function setOptionsCollection($collection): self
    {
        $this->data['options_collection'] = $collection;
        return $this;
    }

    /**
     * Get options IDs for testing
     *
     * @param mixed $product
     * @return array
     */
    public function getOptionsIds($product = null)
    {
        return $this->data['options_ids'] ?? [];
    }

    /**
     * Set options IDs for testing
     *
     * @param array $ids
     * @return self
     */
    public function setOptionsIds(array $ids): self
    {
        $this->data['options_ids'] = $ids;
        return $this;
    }

    /**
     * Get selections collection for testing
     *
     * @param array $optionIds
     * @param mixed $product
     * @return mixed
     */
    public function getSelectionsCollection($optionIds = null, $product = null)
    {
        return $this->data['selections_collection'] ?? null;
    }

    /**
     * Set selections collection for testing
     *
     * @param mixed $collection
     * @return self
     */
    public function setSelectionsCollection($collection): self
    {
        $this->data['selections_collection'] = $collection;
        return $this;
    }

}
