<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Model\Product;

/**
 * Test helper for Magento\Catalog\Model\Product
 *
 * This helper provides custom logic that cannot be achieved with standard PHPUnit mocks:
 * 1. isObjectNew() with call counter - tracks how many times the method is called
 * 2. isRecurring() custom flag - not available in parent Product class
 *
 * All other methods are inherited from the parent Product class or work via DataObject magic methods.
 */
class ProductTestHelper extends Product
{
    /**
     * @var bool
     */
    private $isObjectNew = false;

    /**
     * @var bool
     */
    private $isRecurring = false;

    /**
     * @var int
     */
    private $isObjectNewCallCount = 0;

    /**
     * @var float
     */
    private $cost;

    /**
     * Constructor that accepts optional cost parameter
     *
     * @param float $cost
     */
    public function __construct($cost = 0.0)
    {
        $this->cost = $cost;
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Set is object new
     *
     * @param bool $flag
     * @return $this
     */
    public function setIsObjectNew($flag)
    {
        $this->isObjectNew = $flag;
        return $this;
    }

    /**
     * Is object new with call counter
     *
     * Custom logic: Tracks how many times this method is called
     *
     * @param bool|null $flag
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function isObjectNew($flag = null)
    {
        $this->isObjectNewCallCount++;
        return $this->isObjectNew;
    }

    /**
     * Get is object new call count
     *
     * Custom logic: Returns the number of times isObjectNew() was called
     *
     * @return int
     */
    public function getIsObjectNewCallCount()
    {
        return $this->isObjectNewCallCount;
    }

    /**
     * Reset is object new call count
     *
     * Custom logic: Resets the call counter
     *
     * @return $this
     */
    public function resetIsObjectNewCallCount()
    {
        $this->isObjectNewCallCount = 0;
        return $this;
    }

    /**
     * Set is recurring
     *
     * Custom logic: Sets custom recurring flag (not available in parent)
     *
     * @param bool $value
     * @return $this
     */
    public function setIsRecurring($value)
    {
        $this->isRecurring = $value;
        return $this;
    }

    /**
     * Is recurring
     *
     * Custom logic: Returns custom recurring flag
     *
     * @return bool
     */
    public function isRecurring()
    {
        return $this->isRecurring;
    }

    /**
     * Get is recurring (alias for compatibility)
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsRecurring()
    {
        return $this->isRecurring();
    }

    /**
     * Check if product has options.
     *
     * @return bool
     */
    public function hasOptions(): bool
    {
        return isset($this->_data['has_options']) && $this->_data['has_options'];
    }

    /**
     * Get cost
     *
     * @return float
     */
    public function getCost()
    {
        return $this->cost;
    }
}
