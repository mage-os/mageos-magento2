<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\ConfigurableProduct\Test\Unit\Helper;

use Magento\ConfigurableProduct\Model\Product\Type\Configurable;

/**
 * Test helper for Configurable product type mocking
 */
class ConfigurableTestHelper extends Configurable
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * Skip parent constructor to avoid dependencies
     */
    public function __construct()
    {
        // Skip parent constructor
    }

    /**
     * Set store ID
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->data['store_id'] = $storeId;
        return $this;
    }

    /**
     * Get store ID
     *
     * @return int|null
     */
    public function getStoreId()
    {
        return $this->data['store_id'] ?? null;
    }

    /**
     * Load entity
     *
     * @param mixed $id
     * @param string|null $field
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function load($id, $field = null)
    {
        $this->data['loaded_id'] = $id;
        return $this;
    }

    /**
     * Get type instance
     *
     * @return mixed
     */
    public function getTypeInstance()
    {
        return $this->data['type_instance'] ?? $this;
    }

    /**
     * Get ID field name
     *
     * @return string
     */
    public function getIdFieldName()
    {
        return 'entity_id';
    }

    /**
     * Get data
     *
     * @param string $key
     * @param mixed $index
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getData($key = '', $index = null)
    {
        if ($key === '') {
            return $this->data;
        }
        return $this->data[$key] ?? null;
    }

    /**
     * Get website IDs
     *
     * @return array
     */
    public function getWebsiteIds()
    {
        return $this->data['website_ids'] ?? [];
    }

    /**
     * Set type ID
     *
     * @param string $typeId
     * @return $this
     */
    public function setTypeId($typeId)
    {
        $this->data['type_id'] = $typeId;
        return $this;
    }

    /**
     * Get set attributes
     *
     * @param mixed $product
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getSetAttributes($product)
    {
        return $this->data['set_attributes'] ?? [];
    }

    /**
     * Set type instance for testing
     *
     * @param mixed $typeInstance
     * @return $this
     */
    public function setTypeInstance($typeInstance)
    {
        $this->data['type_instance'] = $typeInstance;
        return $this;
    }
}
