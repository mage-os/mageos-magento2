<?php
/**
 * Copyright 2017 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Model\Category;

/**
 * Test helper for CategoryInterface
 *
 * This helper extends the concrete Category class to provide
 * test-specific functionality without dependency injection issues.
 * 
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class CategoryInterfaceTestHelper extends Category
{
    /**
     * @var mixed
     */
    private $isAnchor = null;

    /**
     * Constructor that skips parent initialization
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Mock method for getIsAnchor
     *
     * @return bool|null
    */
    public function getIsAnchor()
    {
        return $this->isAnchor;
    }

    /**
     * Set the isAnchor value
     *
     * @param bool|null $value
     * @return $this
     */
    public function setIsAnchor($value)
    {
        $this->isAnchor = $value;
        return $this;
    }

    /**
     * Required method from Category
     */
    protected function _construct(): void
    {
        // Mock implementation
    }
}

