<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogRule\Model\ResourceModel\Product;

use Magento\CatalogRule\Model\Indexer\DynamicBatchSizeCalculator;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;

/**
 * Specialized product collection for catalog rule indexing
 */
class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Collection
{
    /**
     * Shared cache of AttributeValuesLoader instances across all collections
     *
     * @var array
     */
    private static array $loaderCache = [];

    /**
     * @var DynamicBatchSizeCalculator
     */
    private $batchSizeCalculator;

    /**
     * Get dynamic batch size calculator
     *
     * @return DynamicBatchSizeCalculator
     */
    private function getBatchSizeCalculator(): DynamicBatchSizeCalculator
    {
        if ($this->batchSizeCalculator === null) {
            $this->batchSizeCalculator = ObjectManager::getInstance()
                ->get(DynamicBatchSizeCalculator::class);
        }
        return $this->batchSizeCalculator;
    }

    /**
     * Get all attribute values for products in collection
     *
     * @param string|AbstractAttribute $attribute
     * @return AttributeValuesLoader
     * @throws LocalizedException
     */
    public function getAllAttributeValues($attribute)
    {
        if (!$attribute instanceof AbstractAttribute) {
            $attribute = $this->getEntity()->getAttribute($attribute);
        }

        $attributeId = (int)$attribute->getId();

        if (!isset(self::$loaderCache[$attributeId])) {
            self::$loaderCache[$attributeId] = new AttributeValuesLoader(
                $this,
                $attribute,
                $this->getBatchSizeCalculator()
            );
        }

        return self::$loaderCache[$attributeId];
    }
}
