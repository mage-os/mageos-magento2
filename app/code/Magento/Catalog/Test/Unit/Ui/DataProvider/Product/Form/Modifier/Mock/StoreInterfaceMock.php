<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Ui\DataProvider\Product\Form\Modifier\Mock;

use Magento\Store\Model\Store;

/**
 * Mock class for StoreInterface with custom test methods
 */
class StoreInterfaceMock extends Store
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

