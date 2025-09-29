<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

use Magento\Quote\Model\Quote;

/**
 * TestHelper for Quote
 * Provides implementation for Quote with additional test methods
 */
class QuoteTestHelper extends Quote
{
    /** @var bool */
    private $inventoryProcessed = false;

    /**
     * Constructor
     */
    public function __construct()
    {
        // Mock implementation - no parent constructor call
    }

    /**
     * Check if inventory processed
     *
     * @return bool
     */
    public function isInventoryProcessed()
    {
        return $this->inventoryProcessed;
    }

    /**
     * Set inventory processed
     *
     * @param bool $inventoryProcessed
     * @return $this
     */
    public function setInventoryProcessed($inventoryProcessed)
    {
        $this->inventoryProcessed = $inventoryProcessed;
        return $this;
    }
}
