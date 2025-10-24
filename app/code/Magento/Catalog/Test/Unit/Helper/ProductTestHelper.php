<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Model\Product;

/**
 * Test helper for Product
 *
 * This helper extends the concrete Product class to provide
 * test-specific functionality without dependency injection issues.
 */
class ProductTestHelper extends Product
{
    /**
     * @var float
     */
    private $cost;

    /**
     * Constructor that accepts cost
     *
     * @param float $cost
     */
    public function __construct($cost)
    {
        $this->cost = $cost;
        // Skip parent constructor to avoid dependency injection issues
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

