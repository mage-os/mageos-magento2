<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Test\Unit\Helper;

use Magento\Framework\Event;

/**
 * TestHelper for Event
 * Provides implementation for Event with additional test methods
 */
class EventTestHelper extends Event
{
    /** @var mixed */
    private $website = null;
    /** @var array */
    private $changedPaths = [];
    /** @var mixed */
    private $product = null;
    /** @var mixed */
    private $collection = null;
    /** @var mixed */
    private $creditmemo = null;
    /** @var mixed */
    private $quote = null;
    /** @var mixed */
    private $item = null;

    /**
     * Get website
     *
     * @return mixed
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Set website
     *
     * @param mixed $website
     * @return $this
     */
    public function setWebsite($website)
    {
        $this->website = $website;
        return $this;
    }

    /**
     * Get changed paths
     *
     * @return array
     */
    public function getChangedPaths()
    {
        return $this->changedPaths;
    }

    /**
     * Set changed paths
     *
     * @param array $changedPaths
     * @return $this
     */
    public function setChangedPaths($changedPaths)
    {
        $this->changedPaths = $changedPaths;
        return $this;
    }

    /**
     * Get product
     *
     * @return mixed
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set product
     *
     * @param mixed $product
     * @return $this
     */
    public function setProduct($product)
    {
        $this->product = $product;
        return $this;
    }

    /**
     * Get collection
     *
     * @return mixed
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * Set collection
     *
     * @param mixed $collection
     * @return $this
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;
        return $this;
    }

    /**
     * Get creditmemo
     *
     * @return mixed
     */
    public function getCreditmemo()
    {
        return $this->creditmemo;
    }

    /**
     * Set creditmemo
     *
     * @param mixed $creditmemo
     * @return $this
     */
    public function setCreditmemo($creditmemo)
    {
        $this->creditmemo = $creditmemo;
        return $this;
    }

    /**
     * Get quote
     *
     * @return mixed
     */
    public function getQuote()
    {
        return $this->quote;
    }

    /**
     * Set quote
     *
     * @param mixed $quote
     * @return $this
     */
    public function setQuote($quote)
    {
        $this->quote = $quote;
        return $this;
    }

    /**
     * Get item
     *
     * @return mixed
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * Set item
     *
     * @param mixed $item
     * @return $this
     */
    public function setItem($item)
    {
        $this->item = $item;
        return $this;
    }
}
