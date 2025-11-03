<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Model\Test\Unit\Helper;

use Magento\Framework\Model\AbstractModel;
use Magento\MediaStorage\Model\File\Storage\Database;

/**
 * Test helper for AbstractModel class
 */
class AbstractModelTestHelper extends AbstractModel
{
    /**
     * @var Database
     */
    private $fileMock;

    /**
     * @var mixed
     */
    private $storeIds = null;

    /**
     * @var mixed
     */
    private $websiteId = null;

    /**
     * @var mixed
     */
    private $id = null;

    /**
     * @var mixed
     */
    private $dataHasChangedForResult = null;

    /**
     * @var mixed
     */
    private $isActive = null;

    /**
     * @var mixed
     */
    private $entityAttributeId = null;

    /**
     * @var mixed
     */
    private $entityTypeId = null;

    /**
     * Constructor - skip parent constructor to avoid dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Load by filename
     *
     * @param string $filename
     * @return Database
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function loadByFileName($filename)
    {
        return $this->fileMock;
    }

    /**
     * Set file mock
     *
     * @param Database $fileMock
     * @return $this
     */
    public function setFileMock($fileMock)
    {
        $this->fileMock = $fileMock;
        return $this;
    }

    /**
     * Get file mock
     *
     * @return Database
     */
    public function getFileMock()
    {
        return $this->fileMock;
    }

    /**
     * Mock method for getStoreIds
     *
     * @return mixed
     */
    public function getStoreIds()
    {
        return $this->storeIds;
    }

    /**
     * Set the store IDs
     *
     * @param mixed $storeIds
     * @return $this
     */
    public function setStoreIds($storeIds)
    {
        $this->storeIds = $storeIds;
        return $this;
    }

    /**
     * Mock method for getWebsiteId
     *
     * @return mixed
     */
    public function getWebsiteId()
    {
        return $this->websiteId;
    }

    /**
     * Set the website ID
     *
     * @param mixed $websiteId
     * @return $this
     */
    public function setWebsiteId($websiteId)
    {
        $this->websiteId = $websiteId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @param string $field
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function dataHasChangedFor($field)
    {
        return $this->dataHasChangedForResult;
    }

    /**
     * @param mixed $result
     * @return $this
     */
    public function setDataHasChangedForResult($result)
    {
        $this->dataHasChangedForResult = $result;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * @param mixed $isActive
     * @return $this
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEntityAttributeId()
    {
        return $this->entityAttributeId;
    }

    /**
     * @param mixed $entityAttributeId
     * @return $this
     */
    public function setEntityAttributeId($entityAttributeId)
    {
        $this->entityAttributeId = $entityAttributeId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEntityTypeId()
    {
        return $this->entityTypeId;
    }

    /**
     * @param mixed $entityTypeId
     * @return $this
     */
    public function setEntityTypeId($entityTypeId)
    {
        $this->entityTypeId = $entityTypeId;
        return $this;
    }
}
