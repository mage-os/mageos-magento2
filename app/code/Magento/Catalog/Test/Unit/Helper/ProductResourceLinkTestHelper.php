<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Framework\Model\AbstractModel;

/**
 * Test helper for Magento\Catalog\Model\ResourceModel\Product\Link
 *
 * Extends AbstractModel to add custom methods for testing
 */
class ProductResourceLinkTestHelper extends AbstractModel
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependencies
    }

    /**
     * Custom getLinkedProductId method for testing
     *
     * @return mixed
     */
    public function getLinkedProductId()
    {
        return $this->data['linked_product_id'] ?? null;
    }

    /**
     * Set linked product id for testing
     *
     * @param mixed $id
     * @return self
     */
    public function setLinkedProductId($id): self
    {
        $this->data['linked_product_id'] = $id;
        return $this;
    }

    /**
     * Custom toArray method for testing
     *
     * @param array $keys
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function toArray(array $keys = [])
    {
        return $this->data['array'] ?? [];
    }

    /**
     * Set array data for testing
     *
     * @param array $array
     * @return self
     */
    public function setArrayData($array): self
    {
        $this->data['array'] = $array;
        return $this;
    }

    /**
     * Custom getLinkTypeId method for testing
     *
     * @return mixed
     */
    public function getLinkTypeId()
    {
        return $this->data['link_type_id'] ?? null;
    }

    /**
     * Set link type id for testing
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
     * Custom getProductId method for testing
     *
     * @return mixed
     */
    public function getProductId()
    {
        return $this->data['product_id'] ?? null;
    }

    /**
     * Set product id for testing
     *
     * @param mixed $productId
     * @return self
     */
    public function setProductId($productId): self
    {
        $this->data['product_id'] = $productId;
        return $this;
    }
}
