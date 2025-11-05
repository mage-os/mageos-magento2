<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Api\Test\Unit\Helper;

use Magento\Framework\Api\ExtensionAttributesInterface;

class ExtensionAttributesTestHelper implements ExtensionAttributesInterface
{
    /**
     * @var mixed
     */
    private $fileInfo = null;

    /**
     * @var mixed
     */
    private $websiteIds = null;

    /**
     * @var mixed
     */
    private $arrayData = null;

    public function __construct()
    {
        // Empty constructor
    }

    /**
     * @return mixed
     */
    public function getFileInfo()
    {
        return $this->fileInfo;
    }

    /**
     * @param mixed $fileInfo
     * @return $this
     */
    public function setFileInfo($fileInfo)
    {
        $this->fileInfo = $fileInfo;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getWebsiteIds()
    {
        return $this->websiteIds;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function setWebsiteIds($value)
    {
        $this->websiteIds = $value;
        return $this;
    }

    /**
     * @return mixed
     */
    public function __toArray()
    {
        return $this->arrayData;
    }

    /**
     * @param mixed $data
     * @return $this
     */
    public function setArrayData($data)
    {
        $this->arrayData = $data;
        return $this;
    }
}

