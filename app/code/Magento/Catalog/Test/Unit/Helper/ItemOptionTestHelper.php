<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Model\Product\Configuration\Item\Option;

/**
 * Test helper for Magento\Catalog\Model\Product\Configuration\Item\Option
 * 
 * Extends Option to add custom methods for testing
 */
class ItemOptionTestHelper extends Option
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
     * Get value for testing
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->data['value'] ?? null;
    }

    /**
     * Set value for testing
     *
     * @param mixed $value
     * @return self
     */
    public function setValue($value): self
    {
        $this->data['value'] = $value;
        return $this;
    }

    /**
     * Get product for testing
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
     * Get code for testing
     *
     * @return mixed
     */
    public function getCode()
    {
        return $this->data['code'] ?? null;
    }

    /**
     * Set code for testing
     *
     * @param mixed $code
     * @return self
     */
    public function setCode($code): self
    {
        $this->data['code'] = $code;
        return $this;
    }

    /**
     * Get item for testing
     *
     * @return mixed
     */
    public function getItem()
    {
        return $this->data['item'] ?? null;
    }

    /**
     * Set item for testing
     *
     * @param mixed $item
     * @return self
     */
    public function setItem($item): self
    {
        $this->data['item'] = $item;
        return $this;
    }
}
