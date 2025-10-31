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
 * Lazy-loading attribute values container with bounded memory usage
 *
 * @implements \ArrayAccess<int, array<int, mixed>>
 * @implements \Countable
 */
class AttributeValuesLoader implements \ArrayAccess, \Countable
{
    /**
     * Number of products to load per batch
     */
    private const BATCH_SIZE = 5000;

    /**
     * Maximum number of batches to keep in memory
     * With BATCH_SIZE=5000, this means ~10k products in memory max
     */
    private const MAX_BATCHES_IN_MEMORY = 2;

    /**
     * @var Collection
     */
    private Collection $collection;

    /**
     * @var AbstractAttribute
     */
    private AbstractAttribute $attribute;

    /**
     * @var array<int, array<int, mixed>>
     */
    private array $loadedData = [];

    /**
     * @var array<int, int>
     */
    private array $entityToBatch = [];

    /**
     * @var array<int, bool>
     */
    private array $loadedBatches = [];

    /**
     * @var array<int>
     */
    private array $batchQueue = [];

    /**
     * @var int|null
     */
    private ?int $totalCount = null;

    /**
     * @var array<int>
     */
    private array $allEntityIds = [];

    /**
     * @param Collection $collection
     * @param AbstractAttribute $attribute
     */
    public function __construct(Collection $collection, AbstractAttribute $attribute)
    {
        $this->collection = $collection;
        $this->attribute = $attribute;
    }

    /**
     * Check if entity has attribute values
     *
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        $this->ensureEntityLoaded((int)$offset);
        return isset($this->loadedData[$offset]);
    }

    /**
     * Get attribute values for entity
     *
     * @param mixed $offset
     * @return array<int, mixed>|null
     */
    public function offsetGet($offset): ?array
    {
        $this->ensureEntityLoaded((int)$offset);
        return $this->loadedData[$offset] ?? null;
    }

    /**
     * Set offset not supported
     *
     * @param mixed $offset
     * @param mixed $value
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function offsetSet($offset, $value): void
    {
        throw new \LogicException('AttributeValuesLoader is read-only');
    }

    /**
     * Offset unset not supported
     *
     * @param mixed $offset
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function offsetUnset($offset): void
    {
        throw new \LogicException('AttributeValuesLoader is read-only');
    }

    /**
     * Get total count of entities
     *
     * @return int
     */
    public function count(): int
    {
        if ($this->totalCount === null) {
            $this->loadEntityIds();
        }
        return $this->totalCount;
    }

    /**
     * Ensure the batch containing the given entity is loaded
     *
     * @param int $entityId
     * @return void
     * @throws LocalizedException
     */
    private function ensureEntityLoaded(int $entityId): void
    {
        $batchNumber = $this->getBatchNumberForEntity($entityId);

        if ($batchNumber === null || isset($this->loadedBatches[$batchNumber])) {
            return;
        }

        $this->loadBatch($batchNumber);

        $this->evictOldBatchesIfNeeded();
    }

    /**
     * Get batch number for entity
     *
     * @param int $entityId
     * @return int|null
     */
    private function getBatchNumberForEntity(int $entityId): ?int
    {
        if (empty($this->allEntityIds)) {
            $this->loadEntityIds();
        }

        if (!isset($this->entityToBatch[$entityId])) {
            return null;
        }

        return $this->entityToBatch[$entityId];
    }

    /**
     * Load all entity IDs and build batch map
     *
     * @return void
     */
    private function loadEntityIds(): void
    {
        $connection = $this->collection->getConnection();
        $select = $connection->select()
            ->from($this->collection->getMainTable(), ['entity_id'])
            ->order('entity_id ASC');

        $this->allEntityIds = $connection->fetchCol($select);
        $this->totalCount = count($this->allEntityIds);

        $batchNumber = 0;
        foreach (array_chunk($this->allEntityIds, self::BATCH_SIZE) as $batchEntityIds) {
            foreach ($batchEntityIds as $entityId) {
                $this->entityToBatch[(int)$entityId] = $batchNumber;
            }
            $batchNumber++;
        }
    }

    /**
     * Load attribute values for a specific batch
     *
     * @param int $batchNumber
     * @return void
     * @throws LocalizedException
     */
    private function loadBatch(int $batchNumber): void
    {
        if (empty($this->allEntityIds)) {
            $this->loadEntityIds();
        }

        $offset = $batchNumber * self::BATCH_SIZE;
        $batchEntityIds = array_slice($this->allEntityIds, $offset, self::BATCH_SIZE);

        if (empty($batchEntityIds)) {
            return;
        }

        $attributeId = (int)$this->attribute->getId();
        $fieldMainTable = $this->collection->getConnection()->getAutoIncrementField(
            $this->collection->getMainTable()
        );
        $fieldJoinTable = $this->attribute->getEntity()->getLinkField();
        $connection = $this->collection->getConnection();

        $select = $connection->select()
            ->from(['cpe' => $this->collection->getMainTable()], ['entity_id'])
            ->join(
                ['cpa' => $this->attribute->getBackend()->getTable()],
                'cpe.' . $fieldMainTable . ' = cpa.' . $fieldJoinTable,
                ['store_id', 'value']
            )
            ->where('attribute_id = ?', $attributeId)
            ->where('cpe.entity_id IN (?)', $batchEntityIds);

        $data = $connection->fetchAll($select);

        foreach ($data as $row) {
            $entityId = (int)$row['entity_id'];
            $storeId = (int)$row['store_id'];
            $this->loadedData[$entityId][$storeId] = $row['value'];
        }

        $this->loadedBatches[$batchNumber] = true;
        $this->batchQueue[] = $batchNumber;

        unset($data);
    }

    /**
     * Evict oldest batches if memory limit exceeded
     *
     * @return void
     */
    private function evictOldBatchesIfNeeded(): void
    {
        while (count($this->batchQueue) > self::MAX_BATCHES_IN_MEMORY) {
            $oldestBatch = array_shift($this->batchQueue);
            unset($this->loadedBatches[$oldestBatch]);

            $offset = $oldestBatch * self::BATCH_SIZE;
            $batchEntityIds = array_slice($this->allEntityIds, $offset, self::BATCH_SIZE);

            foreach ($batchEntityIds as $entityId) {
                unset($this->loadedData[(int)$entityId]);
            }
        }
    }
}
