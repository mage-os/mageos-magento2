<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Model\Product;

/**
 * Test helper for ProductInterface
 *
 * This helper extends the concrete Product class to provide
 * test-specific functionality without dependency injection issues.
 * Extends existing implementation instead of implementing interface from scratch.
 */
class ProductInterfaceTestHelper extends Product
{
    /**
     * @var bool
     */
    private $isObjectNew = true;

    /**
     * @var string|null
     */
    private $typeId = null;

    /**
     * Constructor that skips parent initialization
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Check if object is new
     *
     * @param bool|null $flag
     * @return bool
     */
    public function isObjectNew($flag = null)
    {
        return $this->isObjectNew;
    }

    /**
     * Set is object new
     *
     * @param bool $isNew
     * @return $this
     */
    public function setIsObjectNew($isNew)
    {
        $this->isObjectNew = $isNew;
        return $this;
    }

    /**
     * Get type ID
     *
     * @return string|null
     */
    public function getTypeId()
    {
        return $this->typeId;
    }

    /**
     * Set type ID
     *
     * @param string|null $typeId
     * @return $this
     */
    public function setTypeId($typeId)
    {
        $this->typeId = $typeId;
        return $this;
    }
}

