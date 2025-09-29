<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Test\Unit\Helper;

use Magento\Framework\DataObject;

/**
 * Test helper for Framework DataObject
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class DataObjectTestHelper extends DataObject
{
    /**
     * @var mixed
     */
    private $types = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get types
     *
     * @return mixed
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * Set types
     *
     * @param mixed $types
     * @return $this
     */
    public function setTypes($types): self
    {
        $this->types = $types;
        return $this;
    }

    /**
     * @var array
     */
    private $amountExclTax = [];

    /**
     * @var int
     */
    private $callCount = 0;

    /**
     * Get amount excl tax
     *
     * @return mixed
     */
    public function getAmountExclTax()
    {
        $value = $this->amountExclTax[$this->callCount] ?? null;
        $this->callCount++;
        return $value;
    }

    /**
     * Set amount excl tax
     *
     * @param mixed $amount
     * @return $this
     */
    public function setAmountExclTax($amount)
    {
        $this->amountExclTax[] = $amount;
        return $this;
    }
}
