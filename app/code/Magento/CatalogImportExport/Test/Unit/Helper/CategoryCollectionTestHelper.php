<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogImportExport\Test\Unit\Helper;

use Magento\Catalog\Model\ResourceModel\Category\Collection;

class CategoryCollectionTestHelper extends Collection
{
    /**
     * @var mixed
     */
    private $parentCategory;

    /**
     * @var mixed
     */
    private $childCategory;

    /**
     * @var array
     */
    private $items = [];

    /**
     * @param mixed $parentCategory
     * @param mixed $childCategory
     */
    public function __construct($parentCategory = null, $childCategory = null)
    {
        $this->parentCategory = $parentCategory;
        $this->childCategory = $childCategory;
        if ($parentCategory !== null && $childCategory !== null) {
            $this->items = [$parentCategory, $childCategory];
        }
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * @param mixed $attribute
     * @param mixed $joinType
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addAttributeToSelect($attribute, $joinType = false)
    {
        return $this;
    }

    /**
     * @param mixed $storeId
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setStoreId($storeId)
    {
        return $this;
    }

    /**
     * @param mixed $id
     * @return mixed
     */
    public function getItemById($id)
    {
        return match($id) {
            1 => $this->parentCategory,
            2 => $this->childCategory,
            default => null
        };
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->items);
    }
}

