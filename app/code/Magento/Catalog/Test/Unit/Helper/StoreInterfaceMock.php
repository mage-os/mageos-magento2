<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Store\Model\Store;

/**
 * Test helper for StoreInterface
 *
 * This helper extends the concrete Store class to provide
 * test-specific functionality without dependency injection issues.
 */
class StoreInterfaceMock extends Store
{
    /**
     * Constructor that skips parent initialization
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
