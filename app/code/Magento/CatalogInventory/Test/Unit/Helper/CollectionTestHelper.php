<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogInventory\Test\Unit\Helper;

use Magento\CatalogInventory\Model\ResourceModel\Stock\Collection;

/**
 * TestHelper for Collection
 * Provides implementation for Collection with additional test methods
 */
class CollectionTestHelper extends Collection
{
    /** @var array */
    private $items = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid complex dependencies
    }

    /**
     * Get items
     *
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Set items
     *
     * @param array|null $items
     * @return $this
     */
    public function setItems(?array $items = null)
    {
        $this->items = $items;
        return $this;
    }

    /**
     * Get first item
     *
     * @return mixed
     */
    public function getFirstItem()
    {
        return !empty($this->items) ? reset($this->items) : null;
    }
}
