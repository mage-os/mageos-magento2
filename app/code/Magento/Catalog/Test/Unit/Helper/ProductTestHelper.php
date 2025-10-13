<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Model\Product;
use Magento\Framework\DataObject;

/**
 * Test helper Product providing minimal URL data accessors for tests.
 */
class ProductTestHelper extends Product
{
    /** @var int */
    private $id;

    /** @var DataObject|null */
    private $urlDataObject;

    /**
     * @param int $id
     * @param DataObject|null $urlDataObject
     */
    public function __construct($id, ?DataObject $urlDataObject = null)
    {
        // Intentionally do not call parent constructor; only minimal behavior is required for tests
        $this->id = (int)$id;
        $this->urlDataObject = $urlDataObject;
    }

    /**
     * Get entity id.
     *
     * @return int
     */
    public function getEntityId()
    {
        return $this->id;
    }

    /**
     * Check if URL data object exists.
     *
     * @return bool
     */
    public function hasUrlDataObject()
    {
        return (bool)$this->urlDataObject;
    }

    /**
     * Get URL data object.
     *
     * @return DataObject|null
     */
    public function getUrlDataObject()
    {
        return $this->urlDataObject;
    }

       /**
     * @return bool
     */
    public function isVisibleInSiteVisibility()
    {
        return false;
    }

    /**
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @param DataObject $data
     * @return $this
     */
    public function setUrlDataObject($data)
    {
        $this->urlDataObject = $data;
        return $this;
    }
}
