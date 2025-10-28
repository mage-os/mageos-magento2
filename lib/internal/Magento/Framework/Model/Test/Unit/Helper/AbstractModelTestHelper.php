<?php
/**
 * Copyright 2024 Adobe
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
}
