<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogInventory\Test\Unit\Helper;

/**
 * Test helper for StockItem with getField and getUseConfigField methods
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class StockItemWithFieldTestHelper extends StockItemTestHelper
{
    /**
     * Custom method for tests - always returns 'call-method'
     */
    public function getUseConfigField($fieldName = null)
    {
        return 'call-method';
    }
}

