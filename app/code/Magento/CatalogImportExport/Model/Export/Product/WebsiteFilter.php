<?php
/**
 * Copyright 2021 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogImportExport\Model\Export\Product;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\CatalogImportExport\Model\Export\ProductFilterInterface;

/**
 * Website filter for products export
 */
class WebsiteFilter implements ProductFilterInterface
{
    private const NAME = 'website_id';

    /**
     * @inheritDoc
     */
    public function filter(Collection $collection, array $filters): Collection
    {
        if (!isset($filters[self::NAME])) {
            return $collection;
        }

        $collection->addWebsiteFilter($filters[self::NAME]);
        $collection->setFlag(self::NAME . '_filter_applied');

        return $collection;
    }
}
