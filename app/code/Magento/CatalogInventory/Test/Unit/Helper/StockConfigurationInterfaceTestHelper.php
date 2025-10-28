<?php
/**
 * Copyright 2016 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogInventory\Test\Unit\Helper;

use Magento\CatalogInventory\Model\Configuration;

/**
 * TestHelper for StockConfigurationInterface
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class StockConfigurationInterfaceTestHelper extends Configuration
{
    /** @var array */
    private $testData = [];

    /**
     * Skip parent constructor to avoid dependency injection
     */
    public function __construct()
    {
        // Skip parent to avoid ScopeConfigInterface, StoreManagerInterface, etc.
    }

    /**
     * Override to return test data instead of accessing config
     *
     * @param int|null $store
     * @return float|null
     */
    public function getStockThresholdQty($store = null)
    {
        return $this->testData['stock_threshold_qty'] ?? null;
    }

    /**
     * Setter for test data (not in interface)
     *
     * @param float|null $stockThresholdQty
     * @return $this
     */
    public function setStockThresholdQty($stockThresholdQty)
    {
        $this->testData['stock_threshold_qty'] = $stockThresholdQty;
        return $this;
    }
}
