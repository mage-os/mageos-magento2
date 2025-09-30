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
 * Test helper for creating a lightweight Product instance with controllable fields.
 */
class ProductTestHelper extends Product
{
    /** @var int|string */
    private $id;

    /** @var DataObject|null */
    private $urlDataObject;

    /**
     * @param int|string $id
     * @param DataObject|null $urlDataObject
     */
    public function __construct($id, ?DataObject $urlDataObject = null)
    {
        $this->id = $id;
        $this->urlDataObject = $urlDataObject;
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
     * @return bool
     */
    public function hasUrlDataObject()
    {
        return (bool)$this->urlDataObject;
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

    /**
     * @return DataObject|null
     */
    public function getUrlDataObject()
    {
        return $this->urlDataObject;
    }
}


