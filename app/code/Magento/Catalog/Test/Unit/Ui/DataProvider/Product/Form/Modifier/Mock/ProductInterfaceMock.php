<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Ui\DataProvider\Product\Form\Modifier\Mock;

use Magento\Catalog\Model\Product;

/**
 * Mock class for ProductInterface with custom test methods
 */
class ProductInterfaceMock extends Product
{
    /**
     * Bypass parent constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Initialize resources
     *
     * @return void
     */
    protected function _construct()
    {
        // Mock implementation - no actual resource initialization needed
    }
}

