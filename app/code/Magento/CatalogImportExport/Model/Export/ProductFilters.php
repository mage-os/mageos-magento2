<?php
/**
 * Copyright 2020 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogImportExport\Model\Export;

use Magento\Catalog\Model\ResourceModel\Product\Collection;

/**
 * Product filters pool for export
 */
class ProductFilters implements ProductFilterInterface
{
    /**
     * @var ProductFilterInterface[]
     */
    private $filters;
    /**
     * @param ProductFilterInterface[] $filters
     */
    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * @inheritDoc
     */
    public function filter(Collection $collection, array $filters): Collection
    {
        foreach ($this->filters as $filter) {
            $collection = $filter->filter($collection, $filters);
        }
        return $collection;
    }
}
