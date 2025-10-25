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
 * 1. Constructor that accepts cost parameter - used by TotalsTest
 * 2. getCost() method - called by production code in NegotiableQuote/Model/Quote/Totals
 * 3. hasOptions() method - mocked by SkuTest to control option behavior
 *
 * Other methods (setIsObjectNew, isObjectNew) are inherited from AbstractModel.
 */
class ProductTestHelper extends Product
{
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
     * Convenience wrapper for parent's isObjectNew($flag) method.
     *
     * @param bool $flag
     * @return $this
     */
    public function setIsObjectNew($flag)
    {
        parent::isObjectNew($flag);
        return $this;
    }

    /**
     * Check if product has options
     *
     * This method is mocked by tests to control option behavior.
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
