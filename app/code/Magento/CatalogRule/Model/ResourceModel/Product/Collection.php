<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogRule\Model\ResourceModel\Product;

use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Framework\Exception\LocalizedException;

/**
 * Specialized product collection for catalog rule indexing
 */
class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Collection
{
    /**
     * Maximum number of attribute value rows to load into memory at once
     */
    private const MAX_ATTRIBUTE_VALUE_ROWS = 100000;

    /**
     * Batch size for iterator-based loading
     */
    private const BATCH_SIZE = 1000;

    /**
     * Get all attribute values for products in collection
     *
     * @param string|AbstractAttribute $attribute
     * @return array|AttributeValuesLoader Array for small datasets, lazy loader for large ones
     * @throws LocalizedException
     */
    public function getAllAttributeValues($attribute)
    {
        if (!$attribute instanceof AbstractAttribute) {
            $attribute = $this->getEntity()->getAttribute($attribute);
        }

        if ($this->isAttributeValueSetTooLarge($attribute)) {
            return new AttributeValuesLoader($this, $attribute);
        }

        return iterator_to_array($this->getAllAttributeValuesIterator($attribute));
    }

    /**
     * Check if attribute value set is too large to load into memory
     *
     * @param AbstractAttribute $attribute
     * @return bool
     * @throws LocalizedException
     */
    private function isAttributeValueSetTooLarge(AbstractAttribute $attribute): bool
    {
        $attributeId = (int)$attribute->getId();
        $fieldMainTable = $this->getConnection()->getAutoIncrementField($this->getMainTable());
        $fieldJoinTable = $attribute->getEntity()->getLinkField();

        $countSelect = $this->getConnection()->select()
            ->from(['cpe' => $this->getMainTable()], [])
            ->join(
                ['cpa' => $attribute->getBackend()->getTable()],
                'cpe.' . $fieldMainTable . ' = cpa.' . $fieldJoinTable,
                ['count' => 'COUNT(*)']
            )
            ->where('attribute_id = ?', $attributeId);

        $count = (int)$this->getConnection()->fetchOne($countSelect);

        return $count > self::MAX_ATTRIBUTE_VALUE_ROWS;
    }

    /**
     * Get attribute values as iterator for memory-efficient processing
     *
     * @param string|AbstractAttribute $attribute
     * @return \Generator
     * @throws LocalizedException
     */
    public function getAllAttributeValuesIterator(AbstractAttribute|string $attribute): \Generator
    {
        if (!$attribute instanceof AbstractAttribute) {
            $attribute = $this->getEntity()->getAttribute($attribute);
        }

        $attributeId = (int)$attribute->getId();
        $fieldMainTable = $this->getConnection()->getAutoIncrementField($this->getMainTable());
        $fieldJoinTable = $attribute->getEntity()->getLinkField();

        $select = $this->getConnection()->select()
            ->from(['cpe' => $this->getMainTable()], ['entity_id'])
            ->join(
                ['cpa' => $attribute->getBackend()->getTable()],
                'cpe.' . $fieldMainTable . ' = cpa.' . $fieldJoinTable,
                ['store_id', 'value']
            )
            ->where('attribute_id = ?', $attributeId)
            ->order(['cpe.entity_id ASC', 'cpa.store_id ASC']);

        $lastEntityId = 0;
        $lastStoreId = -1;
        $currentEntityId = null;
        $currentValues = [];

        do {
            $batchSelect = clone $select;
            $connection = $this->getConnection();

            // Compound cursor pagination to prevent data loss across batch boundaries
            // E.g., if product 100 has 10 store values and batch size is 5,
            // simple entity_id > 100 would skip the remaining 5 values
            $condition = sprintf(
                '(cpe.entity_id > %d) OR (cpe.entity_id = %d AND cpa.store_id > %d)',
                (int)$lastEntityId,
                (int)$lastEntityId,
                (int)$lastStoreId
            );
            $batchSelect->where($condition);
            $batchSelect->limit(self::BATCH_SIZE);
            $data = $connection->fetchAll($batchSelect);

            foreach ($data as $row) {
                $entityId = (int)$row['entity_id'];

                if ($currentEntityId !== null && $entityId !== $currentEntityId) {
                    yield $currentEntityId => $currentValues;
                    $currentValues = [];
                }

                $currentEntityId = $entityId;
                $currentValues[(int)$row['store_id']] = $row['value'];
                $lastEntityId = $entityId;
                $lastStoreId = (int)$row['store_id'];

                unset($row);
            }

            $rowCount = count($data);

            if ($rowCount < self::BATCH_SIZE) {
                $lastStoreId = -1;
            }

            unset($data, $batchSelect);

        } while ($rowCount >= self::BATCH_SIZE);

        if ($currentEntityId !== null) {
            yield $currentEntityId => $currentValues;
        }
    }
}
