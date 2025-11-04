<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Event\Test\Unit\Helper;

use Magento\Framework\Event;

/**
 * Test helper for Event class
 */
class EventTestHelper extends Event
{
    /**
     * @var array
     */
    private $data = [];

    public function __construct()
    {
        // Skip parent constructor to avoid dependencies
    }

    /**
     * Get adapter (custom method for testing)
     *
     * @return mixed
     */
    public function getAdapter()
    {
        return $this->data['adapter'] ?? null;
    }

    /**
     * Set adapter (custom method for testing)
     *
     * @param mixed $adapter
     * @return $this
     */
    public function setAdapter($adapter): self
    {
        $this->data['adapter'] = $adapter;
        return $this;
    }

    /**
     * Get bunch (custom method for testing)
     *
     * @return mixed
     */
    public function getBunch()
    {
        return $this->data['bunch'] ?? null;
    }

    /**
     * Set bunch (custom method for testing)
     *
     * @param mixed $bunch
     * @return $this
     */
    public function setBunch($bunch): self
    {
        $this->data['bunch'] = $bunch;
        return $this;
    }

    /**
     * Get category (custom method for testing)
     *
     * @return mixed
     */
    public function getCategory()
    {
        return $this->data['category'] ?? null;
    }

    /**
     * Set category (custom method for testing)
     *
     * @param mixed $category
     * @return $this
     */
    public function setCategory($category): self
    {
        $this->data['category'] = $category;
        return $this;
    }

    /**
     * Get product (custom method for testing)
     *
     * @return mixed
     */
    public function getProduct()
    {
        return $this->data['product'] ?? null;
    }

    /**
     * Set product (custom method for testing)
     *
     * @param mixed $product
     * @return $this
     */
    public function setProduct($product): self
    {
        $this->data['product'] = $product;
        return $this;
    }

    /**
     * Get collection (custom method for testing)
     *
     * @return mixed
     */
    public function getCollection()
    {
        return $this->data['collection'] ?? null;
    }

    /**
     * Set collection (custom method for testing)
     *
     * @param mixed $collection
     * @return $this
     */
    public function setCollection($collection): self
    {
        $this->data['collection'] = $collection;
        return $this;
    }

    /**
     * Get limit (custom method for testing)
     *
     * @return mixed
     */
    public function getLimit()
    {
        return $this->data['limit'] ?? null;
    }

    /**
     * Set limit (custom method for testing)
     *
     * @param mixed $limit
     * @return $this
     */
    public function setLimit($limit): self
    {
        $this->data['limit'] = $limit;
        return $this;
    }

    /**
     * Get items (custom method for testing)
     *
     * @return mixed
     */
    public function getItems()
    {
        return $this->data['items'] ?? null;
    }

    /**
     * Set items (custom method for testing)
     *
     * @param mixed $items
     * @return $this
     */
    public function setItems($items): self
    {
        $this->data['items'] = $items;
        return $this;
    }

    /**
     * Get store (custom method for testing)
     *
     * @return mixed
     */
    public function getStore()
    {
        return $this->data['store'] ?? null;
    }

    /**
     * Set store (custom method for testing)
     *
     * @param mixed $store
     * @return $this
     */
    public function setStore($store): self
    {
        $this->data['store'] = $store;
        return $this;
    }

    /**
     * Get result (custom method for testing)
     *
     * @return mixed
     */
    public function getResult()
    {
        return $this->data['result'] ?? null;
    }

    /**
     * Set result (custom method for testing)
     *
     * @param mixed $result
     * @return $this
     */
    public function setResult($result): self
    {
        $this->data['result'] = $result;
        return $this;
    }

    /**
     * Get quote (custom method for testing)
     *
     * @return mixed
     */
    public function getQuote()
    {
        return $this->data['quote'] ?? null;
    }

    /**
     * Set quote (custom method for testing)
     *
     * @param mixed $quote
     * @return $this
     */
    public function setQuote($quote): self
    {
        $this->data['quote'] = $quote;
        return $this;
    }

    /**
     * Get order (custom method for testing)
     *
     * @return mixed
     */
    public function getOrder()
    {
        return $this->data['order'] ?? null;
    }

    /**
     * Set order (custom method for testing)
     *
     * @param mixed $order
     * @return $this
     */
    public function setOrder($order): self
    {
        $this->data['order'] = $order;
        return $this;
    }
}
