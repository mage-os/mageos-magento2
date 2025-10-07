<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\ConfigurableProduct\Test\Unit\Helper;

use Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute;

/**
 * Test helper for Configurable\Attribute class
 */
class ConfigurableAttributeTestHelper extends Attribute
{
    /**
     * @var mixed
     */
    private $attributeId;

    /**
     * @var mixed
     */
    private $productAttribute;

    /**
     * Skip parent constructor
     */
    public function __construct()
    {
    }

    /**
     * Get attribute ID
     *
     * @return mixed
     */
    public function getAttributeId()
    {
        return $this->attributeId;
    }

    /**
     * Set attribute ID
     *
     * @param mixed $id
     * @return void
     */
    public function setAttributeId($id)
    {
        $this->attributeId = $id;
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
     * @param mixed $attr
     * @return void
     */
    public function setProductAttribute($attr)
    {
        $this->productAttribute = $attr;
    }
}
