<?php
/**
 * Copyright 2020 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogImportExport\Model\Export\Product;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\CatalogImportExport\Model\Export\ProductFilterInterface;

/**
 * Category filter for products export
 */
class CategoryFilter implements ProductFilterInterface
{
    private const NAME = 'category_ids';

    /**
     * @inheritDoc
     */
    public function filter(Collection $collection, array $filters): Collection
    {
        $value = trim($filters[self::NAME] ?? '');
        if ($value) {
            $collection->addCategoriesFilter(['in' => explode(',', $value)]);
            $collection->setFlag(self::NAME . '_filter_applied');
        }
        return $collection;
    }
}
