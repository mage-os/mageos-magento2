<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\ConfigurableProduct\Test\Unit\Helper;

use Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute;

/**
 * Test helper for ConfigurableProduct Attribute/Option mocking
 *
 * Implements OptionInterface and provides common methods for testing
 */
class AttributeTestHelper extends Attribute
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
}
