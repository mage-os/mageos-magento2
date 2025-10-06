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
 */
class AttributeTestHelper extends Attribute
{
    private $data = [];

    /**
     * Skip parent constructor to avoid dependencies
     */
    public function __construct()
    {
        // Skip parent constructor
        $this->data = [];
        // Initialize store manager to prevent null errors
        $this->data['store_manager'] = $this->createMockStoreManager();
        $this->data['resource'] = $this->createMockResource();
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
     */
    public function isInSet($setId)
    {
        return $this->data['is_in_set'] ?? false;
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
     */
    public function getIsUnique()
    {
        return $this->data['is_unique'] ?? false;
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
     * Mock method for testing
     *
     * @return mixed
     */
    public function getFrontend()
    {
        return $this->data['frontend'] ?? null;
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
    public function setStoreLabel($storeLabel)
    {
        $this->data['store_label'] = $storeLabel;
        return $this;
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
    public function setFrontendLabel(string $label)
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
    public function setSource($source)
    {
        $this->data['source'] = $source;
        return $this;
    }

    /**
     * Set label for testing
     *
     * @param string $label
     * @return self
     */
    public function setLabel($label)
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
     * Override getSource to prevent null errors
     *
     * @return mixed
     */
    public function getSource()
    {
        return $this->data['source'] ?? $this->createMockSource();
    }

    /**
     * Create a mock source to prevent null errors
     *
     * @return mixed
     */
    private function createMockSource()
    {
        if (!isset($this->data['source'])) {
            $this->data['source'] = new class {
                public function getAllOptions($withEmpty = true) { return []; }
                public function getOptionText($value) { return 'Test Option'; }
                public function create() { return $this; }
            };
        }
        return $this->data['source'];
    }

    /**
     * Override getEntityType to prevent null errors
     *
     * @return mixed
     */
    public function getEntityType()
    {
        return $this->data['entity_type'] ?? $this->createMockEntityType();
    }

    /**
     * Override getStoreManager to prevent null errors
     *
     * @return mixed
     */
    public function getStoreManager()
    {
        return $this->data['store_manager'] ?? $this->createMockStoreManager();
    }

    /**
     * Override _getStoreManager to prevent null errors (protected method)
     *
     * @return mixed
     */
    protected function _getStoreManager()
    {
        return $this->getStoreManager();
    }

    /**
     * Override getDefaultStoreId to prevent null errors
     *
     * @return int
     */
    public function getDefaultStoreId()
    {
        return 0;
    }

    /**
     * Override _getResource to prevent null errors
     *
     * @return mixed
     */
    protected function _getResource()
    {
        return $this->getResource();
    }

    /**
     * Override getStoreLabel to prevent null errors
     *
     * @param int|null $storeId
     * @return string
     */
    public function getStoreLabel($storeId = null)
    {
        return $this->data['store_label'] ?? $this->getFrontendLabel();
    }

    /**
     * Override _getStoreLabel to prevent null errors
     *
     * @param int|null $storeId
     * @return string
     */
    protected function _getStoreLabel($storeId = null)
    {
        return $this->getStoreLabel($storeId);
    }

    /**
     * Create a mock store manager to prevent null errors
     *
     * @return mixed
     */
    private function createMockStoreManager()
    {
        if (!isset($this->data['store_manager'])) {
            $this->data['store_manager'] = new class {
                public function getStore($storeId = null) {
                    return new class {
                        public function getId() { return 1; }
                        public function getCode() { return 'default'; }
                        public function getName() { return 'Default Store'; }
                        public function getWebsiteId() { return 1; }
                        public function getGroupId() { return 1; }
                    };
                }
                public function getStores($withDefault = false) { return [$this->getStore()]; }
                public function getWebsite($websiteId = null) { return $this->getStore(); }
                public function getGroup($groupId = null) { return $this->getStore(); }
            };
        }
        return $this->data['store_manager'];
    }

    /**
     * Override getResource to prevent null errors
     *
     * @return mixed
     */
    public function getResource()
    {
        return $this->data['resource'] ?? $this->createMockResource();
    }

    /**
     * Create a mock resource to prevent null errors
     *
     * @return mixed
     */
    private function createMockResource()
    {
        if (!isset($this->data['resource'])) {
            $this->data['resource'] = new class {
                public function getStoreManager() {
                    return new class {
                        public function getStore($storeId = null) {
                            return new class {
                                public function getId() { return 1; }
                                public function getCode() { return 'default'; }
                                public function getName() { return 'Default Store'; }
                                public function getWebsiteId() { return 1; }
                                public function getGroupId() { return 1; }
                            };
                        }
                        public function getStores($withDefault = false) { return [$this->getStore()]; }
                        public function getWebsite($websiteId = null) { return $this->getStore(); }
                        public function getGroup($groupId = null) { return $this->getStore(); }
                    };
                }
                public function getConnection() {
                    return new class {
                        public function select() { return $this; }
                        public function from($table) { return $this; }
                        public function where($condition) { return $this; }
                        public function fetchAll() { return []; }
                        public function fetchRow() { return []; }
                        public function fetchOne() { return null; }
                    };
                }
            };
        }
        return $this->data['resource'];
    }

    /**
     * Create a mock entity type to prevent null errors
     *
     * @return mixed
     */
    private function createMockEntityType()
    {
        if (!isset($this->data['entity_type'])) {
            $this->data['entity_type'] = new class {
                public function getEntityTypeCode() { return 'catalog_product'; }
                public function getId() { return 1; }
                public function getEntityTable() { return 'catalog_product_entity'; }
                public function getEntity() { return $this; }
                public function getEntityIdField() { return 'entity_id'; }
                public function getValueTablePrefix() { return 'catalog_product_entity'; }
                public function getDefaultAttributeSourceModel() { return null; }
            };
        }
        return $this->data['entity_type'];
    }

    /**
     * Set entity type for testing
     *
     * @param mixed $entityType
     * @return self
     */
    public function setEntityType($entityType)
    {
        $this->data['entity_type'] = $entityType;
        return $this;
    }
}
