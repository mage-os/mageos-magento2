<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Test\Unit\Helper;

use Magento\Framework\DataObject;

/**
 * Test helper class for DataObject used across Framework and related module tests
 */
class DataObjectTestHelper extends DataObject
{
    /**
     * @var int
     */
    private int $qty = 1;

    /**
     * Constructor - skip parent constructor to avoid dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Set quantity
     *
     * @param int $qty
     * @return $this
     */
    public function setQty($qty): self
    {
        $this->qty = $qty;
        return $this;
    }

    /**
     * Get quantity
     *
     * @return int
     */
    public function getQty(): int
    {
        return $this->qty;
    }
}
