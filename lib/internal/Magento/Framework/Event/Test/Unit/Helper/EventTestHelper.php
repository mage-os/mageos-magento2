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
     * @var mixed
     */
    private $product;

    /**
     * @var mixed
     */
    private $collection;

    /**
     * @var mixed
     */
    private $limit;

    /**
     * @var mixed
     */
    private $items;

    /**
     * @var
     */
    private $store;
    /**
     * @var
     */
    private $result;
    /**
     * @var
     */
    private $quote;
    /**
     * @var
     */
    private $order;

    public function __construct()
    {
        // Skip parent constructor to avoid dependencies
    }

    /**
     * Get product (custom method for testing)
     *
     * @return mixed
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set product (custom method for testing)
     *
     * @param mixed $product
     * @return $this
     */
    public function setProduct($product): self
    {
        $this->product = $product;
        return $this;
    }

    /**
     * Get collection (custom method for testing)
     *
     * @return mixed
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * Set collection (custom method for testing)
     *
     * @param mixed $collection
     * @return $this
     */
    public function setCollection($collection): self
    {
        $this->collection = $collection;
        return $this;
    }

    /**
     * Get limit (custom method for testing)
     *
     * @return mixed
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Set limit (custom method for testing)
     *
     * @param mixed $limit
     * @return $this
     */
    public function setLimit($limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Get items (custom method for testing)
     *
     * @return mixed
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Set items (custom method for testing)
     *
     * @param mixed $items
     * @return $this
     */
    public function setItems($items): self
    {
        $this->items = $items;
        return $this;
    }

    /**
     * Get store (custom method for testing)
     *
     * @return mixed
     */
    public function getStore()
    {
        return $this->store;
    }

    /**
     * Get result (custom method for testing)
     *
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }
    /**
     * Get quote (custom method for testing)
     *
     * @return mixed
     */
    public function getQuote()
    {
        return $this->quote;
    }

    /**
     * Get order (custom method for testing)
     *
     * @return mixed
     */
    public function getOrder()
    {
        return $this->order;
    }
}
