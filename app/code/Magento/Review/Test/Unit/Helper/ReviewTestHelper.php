<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Review\Test\Unit\Helper;

use Magento\Review\Model\Review;

/**
 * Test helper for Review
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class ReviewTestHelper extends Review
{
    /**
     * @var mixed
     */
    private $data = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get data
     *
     * @param mixed $key
     * @param mixed $index
     * @return mixed
     */
    public function getData($key = '', $index = null)
    {
        return $this->data[$key] ?? null;
    }

    /**
     * Set data
     *
     * @param mixed $key
     * @param mixed $value
     * @return $this
     */
    public function setData($key, $value = null)
    {
        if (is_array($key)) {
            $this->data = $key;
        } else {
            $this->data[$key] = $value;
        }
        return $this;
    }

    /**
     * Save method
     *
     * @return $this
     */
    public function save()
    {
        return $this;
    }

    /**
     * Load method
     *
     * @param mixed $modelId
     * @param mixed $field
     * @return $this
     */
    public function load($modelId, $field = null)
    {
        return $this;
    }

    /**
     * Validate method
     *
     * @return bool
     */
    public function validate()
    {
        return true;
    }

    /**
     * Set entity ID
     *
     * @param mixed $entityId
     * @return $this
     */
    public function setEntityId($entityId)
    {
        return $this;
    }

    /**
     * Get entity ID by code
     *
     * @param mixed $entityCode
     * @return int
     */
    public function getEntityIdByCode($entityCode)
    {
        return 1;
    }

    /**
     * Get ID
     *
     * @return int
     */
    public function getId()
    {
        return 1;
    }

    /**
     * Aggregate method
     *
     * @return $this
     */
    public function aggregate()
    {
        return $this;
    }

    /**
     * Unset data
     *
     * @param mixed $key
     * @return $this
     */
    public function unsetData($key = null)
    {
        return $this;
    }

    /**
     * Set entity PK value
     *
     * @param mixed $entityPkValue
     * @return $this
     */
    public function setEntityPkValue($entityPkValue)
    {
        return $this;
    }

    /**
     * Set status ID
     *
     * @param mixed $statusId
     * @return $this
     */
    public function setStatusId($statusId)
    {
        return $this;
    }

    /**
     * Set customer ID
     *
     * @param mixed $customerId
     * @return $this
     */
    public function setCustomerId($customerId)
    {
        return $this;
    }

    /**
     * Set store ID
     *
     * @param mixed $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        return $this;
    }

    /**
     * Set stores
     *
     * @param mixed $stores
     * @return $this
     */
    public function setStores($stores)
    {
        return $this;
    }

    /**
     * @var mixed
     */
    private $resource = null;

    /**
     * Get resource
     *
     * @return mixed
     */
    public function _getResource()
    {
        return $this->resource;
    }

    /**
     * Set resource
     *
     * @param mixed $resource
     * @return $this
     */
    public function setResource($resource)
    {
        $this->resource = $resource;
        return $this;
    }
}
