<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\ConfigurableProduct\Test\Unit\Helper;

use Magento\ConfigurableProduct\Api\Data\OptionInterface;

/**
 * Test helper for ConfigurableProduct Attribute/Option mocking
 *
 * Implements OptionInterface and provides common methods for testing
 */
class ConfigurableAttributeTestHelper implements OptionInterface
{
    /**
     * @var mixed
     */
    private $productAttribute;

    /**
     * @var array
     */
    private $data = [];

    /**
     * Constructor
     *
     * @param mixed $productAttribute
     */
    public function __construct($productAttribute = null)
    {
        $this->productAttribute = $productAttribute;
    }

    /**
     * Get product attribute
     *
     * @return mixed
     */
    public function getProductAttribute()
    {
        return $this->productAttribute;
    }

    /**
     * Set product attribute
     *
     * @param mixed $productAttribute
     * @return self
     */
    public function setProductAttribute($productAttribute): self
    {
        $this->productAttribute = $productAttribute;
        return $this;
    }

    /**
     * Get position
     *
     * @return int
     */
    public function getPosition(): int
    {
        return $this->data['position'] ?? 0;
    }

    /**
     * Set position
     *
     * @param int $position
     * @return self
     */
    public function setPosition($position): self
    {
        $this->data['position'] = $position;
        return $this;
    }

    /**
     * Get attribute ID
     *
     * @return int
     */
    public function getAttributeId(): int
    {
        return $this->data['attribute_id'] ?? 1;
    }

    /**
     * Set attribute ID
     *
     * @param int $id
     * @return self
     */
    public function setAttributeId($id): self
    {
        $this->data['attribute_id'] = $id;
        return $this;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel(): string
    {
        return $this->data['label'] ?? 'Test Label';
    }

    /**
     * Set label
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
     * Get options
     *
     * @return array
     */
    public function getOptions(): array
    {
        return $this->data['options'] ?? [];
    }

    /**
     * Set options
     *
     * @param array $options
     * @return self
     */
    public function setOptions($options): self
    {
        $this->data['options'] = $options;
        return $this;
    }

    /**
     * Get extension attributes
     *
     * @return mixed
     */
    public function getExtensionAttributes()
    {
        return $this->data['extension_attributes'] ?? null;
    }

    /**
     * Set extension attributes
     *
     * @param mixed $extensionAttributes
     * @return self
     */
    public function setExtensionAttributes($extensionAttributes): self
    {
        $this->data['extension_attributes'] = $extensionAttributes;
        return $this;
    }

    /**
     * Get product ID
     *
     * @return int|null
     */
    public function getProductId(): ?int
    {
        return $this->data['product_id'] ?? null;
    }

    /**
     * Set product ID
     *
     * @param int $productId
     * @return self
     */
    public function setProductId($productId): self
    {
        $this->data['product_id'] = $productId;
        return $this;
    }

    /**
     * Get ID
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->data['id'] ?? null;
    }

    /**
     * Set ID
     *
     * @param mixed $id
     * @return self
     */
    public function setId($id): self
    {
        $this->data['id'] = $id;
        return $this;
    }

    /**
     * Get values
     *
     * @return array
     */
    public function getValues(): array
    {
        return $this->data['values'] ?? [];
    }

    /**
     * Set values
     *
     * @param array|null $values
     * @return self
     */
    public function setValues(?array $values = null): self
    {
        $this->data['values'] = $values;
        return $this;
    }

    /**
     * Get is use default
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsUseDefault(): bool
    {
        return $this->data['is_use_default'] ?? false;
    }

    /**
     * Set is use default
     *
     * @param bool $isUseDefault
     * @return self
     */
    public function setIsUseDefault($isUseDefault): self
    {
        $this->data['is_use_default'] = $isUseDefault;
        return $this;
    }
}
