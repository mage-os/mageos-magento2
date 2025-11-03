<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Model\Category;

/**
 * TestHelper for Category with dynamic methods
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class CategoryTestHelper extends Category
{
    /** @var array */
    private $changedProductIds = [];

    /**
     * @var bool
     */
    private $shouldThrowException = false;

    /**
     * @var mixed
     */
    private $filterPriceRange = null;

    /**
     * @var bool
     */
    private $isAnchor = false;

    /**
     * @var string
     */
    private $children = '';

    public function __construct()
    {
        // Skip parent constructor to avoid complex dependencies
    }

    public function getChangedProductIds()
    {
        return $this->changedProductIds;
    }

    public function setChangedProductIds($value)
    {
        $this->changedProductIds = $value;
        return $this;
    }

    public function __wakeUp()
    {
        // Implementation for __wakeUp method
    }

    public function setShouldThrowException($value)
    {
        $this->shouldThrowException = $value;
        return $this;
    }

    public function save()
    {
        if ($this->shouldThrowException) {
            throw new \Exception();
        }
        return $this;
    }

    public function getProductsPosition()
    {
        $array = $this->getData('products_position');
        if ($array === null) {
            $array = [];
        }
        return $array;
    }

    /**
     * @return mixed
     */
    public function getFilterPriceRange()
    {
        return $this->filterPriceRange;
    }

    /**
     * @param mixed $filterPriceRange
     * @return $this
     */
    public function setFilterPriceRange($filterPriceRange)
    {
        $this->filterPriceRange = $filterPriceRange;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsAnchor()
    {
        return $this->isAnchor;
    }

    /**
     * @param bool $isAnchor
     * @return $this
     */
    public function setIsAnchor($isAnchor)
    {
        $this->isAnchor = $isAnchor;
        return $this;
    }

    /**
     * @param bool $recursive
     * @param bool $isActive
     * @param bool $sortByPosition
     * @return string
     */
    public function getChildren($recursive = true, $isActive = true, $sortByPosition = true)
    {
        return $this->children;
    }

    /**
     * @param string $children
     * @return $this
     */
    public function setChildren($children)
    {
        $this->children = $children;
        return $this;
    }
}
