<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

use Magento\Quote\Model\Quote\Item\AbstractItem;

/**
 * Test helper class for AbstractItem with custom methods
 * 
 * This helper is placed in Magento_Quote module as it's the core module
 * that contains the AbstractItem class and is used by many other modules
 * including ConfigurableProduct, Bundle, Tax, SalesRule, etc.
 */
class AbstractItemTestHelper extends AbstractItem
{
    private $product;
    private $children = [];
    private $quote;
    private $address;
    private $options = [];

    /**
     * Skip parent constructor to avoid dependencies
     * 
     * @param mixed $product Optional product to set
     * @param array $children Optional children to set
     */
    public function __construct($product = null, array $children = [])
    {
        $this->product = $product;
        $this->children = $children;
        // Skip parent constructor
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
     * @return self
     */
    public function setProduct($product): self
    {
        $this->product = $product;
        return $this;
    }

    /**
     * Get children items
     *
     * @return array
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * Check if item has children
     *
     * @return bool
     */
    public function getHasChildren(): bool
    {
        return !empty($this->children);
    }

    /**
     * Set children items
     *
     * @param array $children
     * @return self
     */
    public function setChildren(array $children): self
    {
        $this->children = $children;
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
     * @return self
     */
    public function setQuote($quote): self
    {
        $this->quote = $quote;
        return $this;
    }

    /**
     * Get address
     *
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set address
     *
     * @param mixed $address
     * @return self
     */
    public function setAddress($address): self
    {
        $this->address = $address;
        return $this;
    }

    /**
     * Get option by code
     *
     * @param string $code
     * @return mixed
     */
    public function getOptionByCode($code)
    {
        return $this->options[$code] ?? null;
    }

    /**
     * Set option by code
     *
     * @param string $code
     * @param mixed $option
     * @return self
     */
    public function setOptionByCode(string $code, $option): self
    {
        $this->options[$code] = $option;
        return $this;
    }

    /**
     * Set all options
     *
     * @param array $options
     * @return self
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;
        return $this;
    }

    /**
     * Get all options
     *
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
