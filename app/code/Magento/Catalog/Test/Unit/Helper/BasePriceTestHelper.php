<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Pricing\Price\BasePrice;

/**
 * Test helper class for BasePrice with custom methods
 *
 * This helper extends BasePrice to add custom methods
 * that don't exist on the base class for testing purposes.
 */
class BasePriceTestHelper extends BasePrice
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
     * Custom getPriceWithoutOption method for Bundle testing
     *
     * @return mixed
     */
    public function getPriceWithoutOption()
    {
        return $this->data['price_without_option'] ?? null;
    }

    /**
     * Set price without option for testing
     *
     * @param mixed $price
     * @return self
     */
    public function setPriceWithoutOption($price): self
    {
        $this->data['price_without_option'] = $price;
        return $this;
    }

    /**
     * Override getAmount for testing
     *
     * @return mixed
     */
    public function getAmount()
    {
        return $this->data['amount'] ?? null;
    }

    /**
     * Set amount for testing
     *
     * @param mixed $amount
     * @return self
     */
    public function setAmount($amount): self
    {
        $this->data['amount'] = $amount;
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
     * Magic method to handle any method calls dynamically
     *
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        // If it's a getter, return the stored data
        if (strpos($method, 'get') === 0) {
            $key = strtolower(substr($method, 3));
            return $this->data[$key] ?? null;
        }

        // If it's a setter, store the data
        if (strpos($method, 'set') === 0) {
            $key = strtolower(substr($method, 3));
            $this->data[$key] = $args[0] ?? null;
            return $this;
        }

        return null;
    }
}
