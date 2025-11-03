<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogImportExport\Test\Unit\Helper;

use Magento\Tax\Model\ResourceModel\TaxClass\Collection;

class TaxClassCollectionTestHelper extends Collection
{
    /**
     * @var mixed
     */
    private $taxClass;

    /**
     * @var array
     */
    private $items = [];

    /**
     * @param mixed $taxClass
     */
    public function __construct($taxClass = null)
    {
        $this->taxClass = $taxClass;
        if ($taxClass !== null) {
            $this->items = [$taxClass];
        }
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * @param mixed $field
     * @param mixed $condition
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addFieldToFilter($field, $condition = null)
    {
        return $this;
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->items);
    }
}

