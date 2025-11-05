<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Model\Product\Link;

/**
 * Test helper for Magento\Catalog\Model\Product\Link
 *
 * Extends Link to add custom methods for testing
 */
class ProductLinkTestHelper extends Link
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * @var string
     */
    private $linkedProductId;

    /**
     * @var array
     */
    private $arrayData = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependencies
    }

    /**
     * Custom setLinkTypeId method for testing
     *
     * @param mixed $linkTypeId
     * @return self
     */
    public function setLinkTypeId($linkTypeId): self
    {
        $this->data['link_type_id'] = $linkTypeId;
        return $this;
    }

    /**
     * Get link type id for testing
     *
     * @return mixed
     */
    public function getLinkTypeId()
    {
        return $this->data['link_type_id'] ?? null;
    }

    /**
     * Get product collection (override parent method)
     *
     * @return mixed
     */
    public function getProductCollection()
    {
        return $this->data['product_collection'] ?? null;
    }

    /**
     * Set product collection for testing
     *
     * @param mixed $collection
     * @return self
     */
    public function setProductCollection($collection): self
    {
        $this->data['product_collection'] = $collection;
        return $this;
    }

    /**
     * Get linked product ID
     *
     * @return string
     */
    public function getLinkedProductId()
    {
        return $this->linkedProductId;
    }

    /**
     * Set linked product ID
     *
     * @param string $id
     * @return void
     */
    public function setLinkedProductId($id)
    {
        $this->linkedProductId = $id;
    }

    /**
     * Convert to array
     *
     * @param array $keys
     * @return array
     */
    public function toArray(array $keys = [])
    {
        return $this->arrayData;
    }

    /**
     * Set array data
     *
     * @param array $data
     * @return void
     */
    public function setArrayData(array $data)
    {
        $this->arrayData = $data;
    }
}
