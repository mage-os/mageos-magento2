<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Test\Unit\Helper;

use Magento\Framework\DataObject;

/**
 * Test helper for Magento\Framework\DataObject
 *
 * Extends the DataObject class to add custom methods for testing
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class DataObjectTestHelper extends DataObject
{
    /**
     * @var array
     */
    private $data = [];
    /**
     * @var array
     */
    private $testData = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependencies
    }

    /**
     * Custom getValue method for testing
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->testData['value'] ?? null;
    }

    /**
     * Set value for testing
     *
     * @param mixed $value
     * @return self
     */
    public function setValue($value): self
    {
        $this->testData['value'] = $value;
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
        $this->testData[$key] = $value;
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
        return $this->testData[$key] ?? null;
    }

    /**
     * Custom getItems method for testing
     *
     * @return mixed
     */
    public function getItems()
    {
        return $this->testData['items'] ?? [];
    }

    /**
     * Set items for testing
     *
     * @param mixed $items
     * @return self
     */
    public function setItems($items): self
    {
        $this->testData['items'] = $items;
        return $this;
    }

    /**
     * Custom getOptionId method for testing
     *
     * @return mixed
     */
    public function getOptionId()
    {
        return $this->testData['option_id'] ?? null;
    }

    /**
     * Set option ID for testing
     *
     * @param mixed $optionId
     * @return self
     */
    public function setOptionId($optionId): self
    {
        $this->testData['option_id'] = $optionId;
        return $this;
    }

    /**
     * Custom getBundleOption method for testing
     *
     * @return mixed
     */
    public function getBundleOption()
    {
        return $this->testData['bundle_option'] ?? null;
    }

    /**
     * Set bundle option for testing
     *
     * @param mixed $bundleOption
     * @return self
     */
    public function setBundleOption($bundleOption): self
    {
        $this->testData['bundle_option'] = $bundleOption;
        return $this;
    }

    /**
     * Custom getBundleOptionQty method for testing
     *
     * @return mixed
     */
    public function getBundleOptionQty()
    {
        return $this->testData['bundle_option_qty'] ?? [];
    }

    /**
     * Set bundle option qty for testing
     *
     * @param mixed $bundleOptionQty
     * @return self
     */
    public function setBundleOptionQty($bundleOptionQty): self
    {
        $this->testData['bundle_option_qty'] = $bundleOptionQty;
        return $this;
    }

    /**
     * Custom getOptions method for testing
     *
     * @return mixed
     */
    public function getOptions()
    {
        return $this->testData['options'] ?? null;
    }

    /**
     * Set options for testing
     *
     * @param mixed $options
     * @return self
     */
    public function setOptions($options): self
    {
        $this->testData['options'] = $options;
        return $this;
    }

    /**
     * Custom getSuperProductConfig method for testing
     *
     * @return array
     */
    public function getSuperProductConfig(): array
    {
        return $this->testData['super_product_config'] ?? [];
    }

    /**
     * Set super product config for testing
     *
     * @param array $config
     * @return self
     */
    public function setSuperProductConfig(array $config): self
    {
        $this->testData['super_product_config'] = $config;
        return $this;
    }

    /**
     * Custom unsetData method for testing
     *
     * @param mixed $key
     * @return self
     */
    public function unsetData($key = null): self
    {
        if ($key === null) {
            $this->testData = [];
        } else {
            unset($this->testData[$key]);
        }
        return $this;
    }

    /**
     * Custom getQty method for testing
     *
     * @return mixed
     */
    public function getQty()
    {
        return $this->testData['qty'] ?? null;
    }

    /**
     * Set qty for testing
     *
     * @param mixed $qty
     * @return self
     */
    public function setQty($qty): self
    {
        $this->testData['qty'] = $qty;
        return $this;
    }

    /**
     * Custom getBundleOptionsData method for testing
     *
     * @return mixed
     */
    public function getBundleOptionsData()
    {
        return $this->testData['bundle_options_data'] ?? null;
    }

    /**
     * Set bundle options data for testing
     *
     * @param mixed $data
     * @return self
     */
    public function setBundleOptionsData($data): self
    {
        $this->testData['bundle_options_data'] = $data;
        return $this;
    }

    /**
     * Check if salable for testing (selection method)
     *
     * @return bool
     */
    public function isSalable(): bool
    {
        return $this->testData['is_salable'] ?? false;
    }

    /**
     * Set salable status for testing
     *
     * @param bool $value
     * @return self
     */
    public function setIsSalable(bool $value): self
    {
        $this->testData['is_salable'] = $value;
        return $this;
    }

    /**
     * Get selection can change qty for testing
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getSelectionCanChangeQty(): bool
    {
        return $this->testData['selection_can_change_qty'] ?? false;
    }

    /**
     * Set selection can change qty for testing
     *
     * @param bool $value
     * @return self
     */
    public function setSelectionCanChangeQty(bool $value): self
    {
        $this->testData['selection_can_change_qty'] = $value;
        return $this;
    }

    /**
     * Get selection ID for testing
     *
     * @return mixed
     */
    public function getSelectionId()
    {
        return $this->testData['selection_id'] ?? null;
    }

    /**
     * Set selection ID for testing
     *
     * @param mixed $id
     * @return self
     */
    public function setSelectionId($id): self
    {
        $this->testData['selection_id'] = $id;
        return $this;
    }

    /**
     * Add custom option for testing
     *
     * @param mixed $option
     * @return self
     */
    public function addCustomOption($option): self
    {
        if (!isset($this->testData['custom_options'])) {
            $this->testData['custom_options'] = [];
        }
        $this->testData['custom_options'][] = $option;
        return $this;
    }

    /**
     * Get type instance for testing
     *
     * @return mixed
     */
    public function getTypeInstance()
    {
        return $this->testData['type_instance'] ?? null;
    }

    /**
     * Set type instance for testing
     *
     * @param mixed $typeInstance
     * @return self
     */
    public function setTypeInstance($typeInstance): self
    {
        $this->testData['type_instance'] = $typeInstance;
        return $this;
    }

    /**
     * Get option for testing
     *
     * @return mixed
     */
    public function getOption()
    {
        return $this->testData['option'] ?? null;
    }

    /**
     * Set option for testing
     *
     * @param mixed $option
     * @return self
     */
    public function setOption($option): self
    {
        $this->testData['option'] = $option;
        return $this;
    }

    /**
     * Override getId to work without constructor
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->testData['id'] ?? null;
    }

    /**
     * Override setId to work without constructor
     *
     * @param mixed $value
     * @return self
     */
    public function setId($value): self
    {
        $this->testData['id'] = $value;
        return $this;
    }

    /**
     * Get SKU for testing
     *
     * @return mixed
     */
    public function getSku()
    {
        return $this->testData['sku'] ?? null;
    }

    /**
     * Set SKU for testing
     *
     * @param mixed $sku
     * @return self
     */
    public function setSku($sku): self
    {
        $this->testData['sku'] = $sku;
        return $this;
    }

    /**
     * Get entity ID for testing
     *
     * @return mixed
     */
    public function getEntityId()
    {
        return $this->testData['entity_id'] ?? null;
    }

    /**
     * Set entity ID for testing
     *
     * @param mixed $entityId
     * @return self
     */
    public function setEntityId($entityId): self
    {
        $this->testData['entity_id'] = $entityId;
        return $this;
    }

    /**
     * Get weight for testing
     *
     * @return mixed
     */
    public function getWeight()
    {
        return $this->testData['weight'] ?? null;
    }

    /**
     * Set weight for testing
     *
     * @param mixed $weight
     * @return self
     */
    public function setWeight($weight): self
    {
        $this->testData['weight'] = $weight;
        return $this;
    }

    /**
     * Check if virtual for testing
     *
     * @return bool
     */
    public function isVirtual(): bool
    {
        return $this->testData['is_virtual'] ?? false;
    }

    /**
     * Set is virtual for testing
     *
     * @param bool $isVirtual
     * @return self
     */
    public function setIsVirtual(bool $isVirtual): self
    {
        $this->testData['is_virtual'] = $isVirtual;
        return $this;
    }

    /**
     * Get required for testing
     *
     * @return mixed
     */
    public function getRequired()
    {
        return $this->testData['required'] ?? null;
    }

    /**
     * Set required for testing
     *
     * @param mixed $required
     * @return self
     */
    public function setRequired($required): self
    {
        $this->testData['required'] = $required;
        return $this;
    }

    /**
     * Get super attribute
     *
     * @return mixed
     */
    public function getSuperAttribute()
    {
        return $this->testData['super_attribute'] ?? null;
    }

    /**
     * Set super attribute
     *
     * @param mixed $value
     * @return self
     */
    public function setSuperAttribute($value): self
    {
        $this->testData['super_attribute'] = $value;
        return $this;
    }

    /**
     * Get error
     *
     * @return mixed
     */
    public function getError()
    {
        return $this->testData['error'] ?? null;
    }

    /**
     * Set error
     *
     * @param mixed $error
     * @return self
     */
    public function setError($error): self
    {
        $this->testData['error'] = $error;
        return $this;
    }

    /**
     * Get message
     *
     * @return mixed
     */
    public function getMessage()
    {
        return $this->testData['message'] ?? null;
    }

    /**
     * Set message
     *
     * @param mixed $message
     * @return self
     */
    public function setMessage($message): self
    {
        $this->testData['message'] = $message;
        return $this;
    }

    /**
     * Get attributes
     *
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->testData['attributes'] ?? [];
    }

    /**
     * Set attributes
     *
     * @param array $attributes
     * @return self
     */
    public function setAttributes($attributes): self
    {
        $this->testData['attributes'] = $attributes;
        return $this;
    }

    /**
     * Custom setIsAllowed method for testing
     *
     * @param bool $allowed
     * @return self
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setIsAllowed($allowed): self
    {
        return $this;
    }

    /**
     * Custom getLinks method for testing
     *
     * @return mixed
     */
    public function getLinks()
    {
        return null;
    }

    /**
     * Override addData method
     *
     * @param array $data
     * @return self
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addData($data): self
    {
        return $this;
    }

    /**
     * Get name method for testing
     *
     * @return mixed
     */
    public function getName()
    {
        return $this->data['name'] ?? null;
    }

    /**
     * Set name method for testing
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
     * Get price method for testing
     *
     * @return mixed
     */
    public function getPrice()
    {
        return $this->data['price'] ?? null;
    }

    /**
     * Set price method for testing
     *
     * @param mixed $price
     * @return self
     */
    public function setPrice($price): self
    {
        $this->data['price'] = $price;
        return $this;
    }

    /**
     * Get image method for testing
     *
     * @return mixed
     */
    public function getImage()
    {
        return $this->data['image'] ?? null;
    }

    /**
     * Set image method for testing
     *
     * @param mixed $image
     * @return self
     */
    public function setImage($image): self
    {
        $this->data['image'] = $image;
        return $this;
    }

    /**
     * Get position method for testing
     *
     * @return mixed
     */
    public function getPosition()
    {
        return $this->data['position'] ?? null;
    }

    /**
     * Set position method for testing
     *
     * @param mixed $position
     * @return self
     */
    public function setPosition($position): self
    {
        $this->data['position'] = $position;
        return $this;
    }
}
