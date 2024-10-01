<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Model\ResourceModel\Db\VersionControl;

use Magento\Framework\DataObject;
use Magento\Framework\ObjectManager\ResetAfterRequestInterface;

/**
 * Class Snapshot register snapshot of entity data, for tracking changes
 */
class Snapshot implements ResetAfterRequestInterface
{
    /**
     * Array of snapshots of entities data
     *
     * @var array
     */
    protected $snapshotData = [];

    /**
     * @var Metadata
     */
    protected $metadata;

    /**
     * Initialization
     *
     * @param Metadata $metadata
     */
    public function __construct(
        Metadata $metadata
    ) {
        $this->metadata = $metadata;
    }

    /**
     * Register snapshot of entity data, for tracking changes
     *
     * @param DataObject $entity
     * @return void
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function registerSnapshot(DataObject $entity)
    {
        $metaData = $this->metadata->getFields($entity);
        $filteredData = array_intersect_key($entity->getData(), $metaData);
        $data = array_merge($metaData, $filteredData);
        $this->snapshotData[get_class($entity)][$entity->getId()] = $data;
    }

    /**
     * Get snapshot data
     *
     * @param DataObject $entity
     * @return array
     */
    public function getSnapshotData(DataObject $entity): array
    {
        $entityClass = get_class($entity);
        $entityId = $entity->getId();

        if (isset($this->snapshotData[$entityClass][$entityId])) {
            return $this->snapshotData[$entityClass][$entityId];
        }

        return [];
    }

    /**
     * Check is current entity has changes, by comparing current object state with stored snapshot
     *
     * @param DataObject $entity
     * @return bool
     */
    public function isModified(DataObject $entity)
    {
        if (!$entity->getId()) {
            return true;
        }

        $entityClass = get_class($entity);
        if (!isset($this->snapshotData[$entityClass][$entity->getId()])) {
            return true;
        }
        foreach ($this->snapshotData[$entityClass][$entity->getId()] as $field => $value) {
            $entityValue = $entity->getDataByKey($field);
            if (is_array($entityValue) && is_string($value)) {
                $decodedValue = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $value = $decodedValue;
                }
            }
            if ($entityValue != $value) {
                return true;
            }
        }

        return false;
    }

    /**
     * Clear snapshot data
     *
     * @param DataObject|null $entity
     */
    public function clear(DataObject $entity = null)
    {
        if ($entity !== null) {
            $this->snapshotData[get_class($entity)] = [];
        } else {
            $this->snapshotData = [];
        }
    }

    /**
     * @inheritDoc
     */
    public function _resetState(): void
    {
        $this->snapshotData = [];
    }
}
