<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogImportExport\Test\Unit\Helper;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\ImportExport\Model\ResourceModel\CollectionByPagesIterator;

/**
 * Test helper for CollectionByPagesIterator - extends concrete implementation
 */
class CollectionIteratorTestHelper extends CollectionByPagesIterator
{
    /**
     * @var callable|null
     */
    private $iterateCallback = null;

    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Override to use test callback instead of database iteration
     * 
     * @param AbstractDb $collection
     * @param int $pageSize
     * @param array $callbacks
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function iterate(\Magento\Framework\Data\Collection\AbstractDb $collection, $pageSize, array $callbacks)
    {
        if ($this->iterateCallback) {
            call_user_func($this->iterateCallback, $collection, $pageSize, $callbacks);
        }
    }

    /**
     * @param callable $callback
     * @return $this
     */
    public function setIterateCallback($callback)
    {
        $this->iterateCallback = $callback;
        return $this;
    }
}

