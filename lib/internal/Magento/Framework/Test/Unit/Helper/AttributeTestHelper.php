<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Test\Unit\Helper;

use Magento\Eav\Model\Entity\Attribute;

/**
 * Test helper class for EAV Attribute with custom methods
 *
 * This helper extends the EAV Attribute class to provide custom methods
 * needed for testing that don't exist in the parent class.
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class AttributeTestHelper extends Attribute
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
        // Skip parent constructor - clean initialization
        $this->data = [];
    }

    /**
     * Override getId to work without constructor
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->data['id'] ?? $this->data['attribute_id'] ?? null;
    }

    /**
     * Override setId to work without constructor
     *
     * @param mixed $value
     * @return self
     */
    public function setId($value): self
    {
        $this->data['id'] = $value;
        $this->data['attribute_id'] = $value;
        return $this;
    }

    /**
     * Custom method for ConfigurableProduct tests
     *
     * @param mixed $groupId
     * @return self
     */
    public function setAttributeGroupId($groupId): self
    {
        $this->data['attribute_group_id'] = $groupId;
        return $this;
    }

    /**
     * Custom method for ConfigurableProduct tests
     *
     * @return mixed
     */
    public function getAttributeGroupId()
    {
        return $this->data['attribute_group_id'] ?? null;
    }

    /**
     * Custom method for ConfigurableProduct tests
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsVisible()
    {
        return $this->data['is_visible'] ?? true;
    }

    /**
     * Custom method for ConfigurableProduct tests
     *
     * @param bool $isVisible
     * @return self
     */
    public function setIsVisible(bool $isVisible): self
    {
        $this->data['is_visible'] = $isVisible;
        return $this;
    }

    /**
     * Override getData to work with our data array
     *
     * @param string $key
     * @param mixed $index
     * @return mixed
     */
    public function getData($key = '', $index = null)
    {
        if ($key === '') {
            return $this->data;
        }

        if ($index !== null) {
            return $this->data[$key][$index] ?? null;
        }

        return $this->data[$key] ?? null;
    }

    /**
     * Override setData to work with our data array
     *
     * @param string|array $key
     * @param mixed $value
     * @return self
     */
    public function setData($key, $value = null): self
    {
        if (is_array($key)) {
            $this->data = array_merge($this->data, $key);
        } else {
            $this->data[$key] = $value;
        }
        return $this;
    }

    /**
     * Override addData to work with our data array
     *
     * @param array $data
     * @return self
     */
    public function addData(array $data): self
    {
        $this->data = array_merge($this->data, $data);
        return $this;
    }

    /**
     * Get attribute name
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->data['name'] ?? null;
    }

    /**
     * Set attribute name
     *
     * @param mixed $name
     * @return self
     */
    public function setName($name): self
    {
        $this->data['name'] = $name;
        return $this;
    }

    /**
     * Mock method for testing
     *
     * @param mixed $setId
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function isInSet($setId)
    {
        return $this->data['is_in_set'] ?? false;
    }

    /**
     * Set is in set for testing
     *
     * @param bool $isInSet
     * @return self
     */
    public function setIsInSet(bool $isInSet): self
    {
        $this->data['is_in_set'] = $isInSet;
        return $this;
    }

    /**
     * Mock method for testing
     *
     * @param mixed $setId
     * @return self
     */
    public function setAttributeSetId($setId): self
    {
        $this->data['attribute_set_id'] = $setId;
        return $this;
    }

    /**
     * Get attribute set ID
     *
     * @return mixed
     */
    public function getAttributeSetId()
    {
        return $this->data['attribute_set_id'] ?? null;
    }

    /**
     * Mock method for testing
     *
     * @return self
     */
    public function save(): self
    {
        return $this;
    }

    /**
     * Mock method for testing
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsUnique()
    {
        return $this->data['is_unique'] ?? false;
    }

    /**
     * Set is unique for testing
     *
     * @param bool $isUnique
     * @return self
     */
    public function setIsUnique($isUnique): self
    {
        $this->data['is_unique'] = $isUnique;
        return $this;
    }

    /**
     * Mock method for testing
     *
     * @return string
     */
    public function getAttributeCode()
    {
        return $this->data['attribute_code'] ?? 'some_code';
    }

    /**
     * Set attribute code for testing
     *
     * @param string $data
     * @return self
     */
    public function setAttributeCode($data): self
    {
        $this->data['attribute_code'] = $data;
        return $this;
    }

    /**
     * Mock method for testing
     *
     * @return mixed
     */
    public function getFrontend()
    {
        return $this->data['frontend'] ?? null;
    }

    /**
     * Set frontend for testing
     *
     * @param mixed $frontend
     * @return self
     */
    public function setFrontend($frontend): self
    {
        $this->data['frontend'] = $frontend;
        return $this;
    }

    /**
     * Custom method for ConfigurableProduct tests
     *
     * @param mixed $storeId
     * @return self
     */
    public function setStoreId($storeId): self
    {
        $this->data['store_id'] = $storeId;
        return $this;
    }

    /**
     * Custom method for ConfigurableProduct tests
     *
     * @return mixed
     */
    public function getStoreId()
    {
        return $this->data['store_id'] ?? null;
    }

    /**
     * Custom method for ConfigurableProduct tests
     *
     * @return mixed
     */
    public function getProductAttribute()
    {
        return $this->data['product_attribute'] ?? null;
    }

    /**
     * Custom method for ConfigurableProduct tests
     *
     * @param mixed $productAttribute
     * @return self
     */
    public function setProductAttribute($productAttribute): self
    {
        $this->data['product_attribute'] = $productAttribute;
        return $this;
    }

    /**
     * Mock method for testing
     *
     * @param mixed $productId
     * @return self
     */
    public function setProductId($productId): self
    {
        $this->data['product_id'] = $productId;
        return $this;
    }

    /**
     * Mock method for testing
     *
     * @return mixed
     */
    public function getProductId()
    {
        return $this->data['product_id'] ?? null;
    }

    /**
     * Custom method for ConfigurableAttributeData tests
     *
     * @return string
     */
    public function getAttributeLabel()
    {
        return $this->data['attribute_label'] ?? 'Test Label';
    }

    /**
     * Custom method for ConfigurableAttributeData tests
     *
     * @param string $label
     * @return self
     */
    public function setAttributeLabel(string $label): self
    {
        $this->data['attribute_label'] = $label;
        return $this;
    }

    /**
     * Custom method for ResourceModel Attribute tests
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getUseDefault()
    {
        return $this->data['use_default'] ?? false;
    }

    /**
     * Custom method for ResourceModel Attribute tests
     *
     * @param bool $useDefault
     * @return self
     */
    public function setUseDefault($useDefault): self
    {
        $this->data['use_default'] = $useDefault;
        return $this;
    }

    /**
     * Custom method for ConfigurableAttributeData tests
     *
     * @param mixed $storeLabel
     * @return self
     */
    public function setStoreLabel($storeLabel): self
    {
        $this->data['store_label'] = $storeLabel;
        return $this;
    }

    /**
     * Get store label for testing
     *
     * @param int|null $storeId
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getStoreLabel($storeId = null)
    {
        return $this->data['store_label'] ?? $this->getFrontendLabel();
    }

    /**
     * Custom method for AttributesList tests
     *
     * @return string
     */
    public function getFrontendLabel()
    {
        return $this->data['frontend_label'] ?? 'Test Frontend Label';
    }

    /**
     * Custom method for AttributesList tests
     *
     * @param string $label
     * @return self
     */
    public function setFrontendLabel(string $label): self
    {
        $this->data['frontend_label'] = $label;
        return $this;
    }

    /**
     * Custom method for AttributesList tests
     *
     * @param mixed $source
     * @return self
     */
    public function setSource($source): self
    {
        $this->data['source'] = $source;
        return $this;
    }

    /**
     * Get source for testing
     *
     * @return mixed
     */
    public function getSource()
    {
        return $this->data['source'] ?? null;
    }

    /**
     * Set label for testing
     *
     * @param string $label
     * @return self
     */
    public function setLabel($label): self
    {
        $this->data['label'] = $label;
        return $this;
    }

    /**
     * Get label for testing
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->data['label'] ?? 'Test Label';
    }

    /**
     * Set entity type for testing
     *
     * @param mixed $entityType
     * @return self
     */
    public function setEntityType($entityType): self
    {
        $this->data['entity_type'] = $entityType;
        return $this;
    }

    /**
     * Get entity type for testing
     *
     * @return mixed
     */
    public function getEntityType()
    {
        return $this->data['entity_type'] ?? null;
    }

    /**
     * Set store manager for testing
     *
     * @param mixed $storeManager
     * @return self
     */
    public function setStoreManager($storeManager): self
    {
        $this->data['store_manager'] = $storeManager;
        return $this;
    }

    /**
     * Get store manager for testing
     *
     * @return mixed
     */
    public function getStoreManager()
    {
        return $this->data['store_manager'] ?? null;
    }

    /**
     * Set resource for testing
     *
     * @param mixed $resource
     * @return self
     */
    public function setResource($resource): self
    {
        $this->data['resource'] = $resource;
        return $this;
    }

    /**
     * Get resource for testing
     *
     * @return mixed
     */
    public function getResource()
    {
        return $this->data['resource'] ?? null;
    }

    /**
     * Get default store ID for testing
     *
     * @return int
     */
    public function getDefaultStoreId()
    {
        return $this->data['default_store_id'] ?? 0;
    }

    /**
     * Set default store ID for testing
     *
     * @param int $storeId
     * @return self
     */
    public function setDefaultStoreId(int $storeId): self
    {
        $this->data['default_store_id'] = $storeId;
        return $this;
    }

    /**
     * Set test data for flexible state management
     *
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function setTestData(string $key, $value): self
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * Get test data
     *
     * @param string $key
     * @return mixed
     */
    public function getTestData(string $key)
    {
        return $this->data[$key] ?? null;
    }
}
