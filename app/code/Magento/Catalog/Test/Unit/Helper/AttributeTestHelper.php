<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Model\ResourceModel\Eav\Attribute;

/**
 * TestHelper for Attribute with custom methods
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class AttributeTestHelper extends Attribute
{
    /** @var array */
    private $attributesByCode = [];
    /** @var array */
    private $dataUsingMethod = [];

    public function __construct()
    {
        $this->_data = [];
    }

    /**
     * Get attributes by code (custom method)
     *
     * @return array
     */
    public function getAttributesByCode()
    {
        return $this->attributesByCode;
    }

    /**
     * Set attributes by code (custom method)
     *
     * @param array $value
     * @return $this
     */
    public function setAttributesByCode($value)
    {
        $this->attributesByCode = $value;
        return $this;
    }

    /**
     * Get data using method (custom method for dynamic method calls)
     *
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public function getDataUsingMethod($method, $args = [])
    {
        return isset($this->dataUsingMethod[$method]) ? $this->dataUsingMethod[$method] : null;
    }

    /**
     * Set data using method (custom method for dynamic method calls)
     *
     * @param string $key
     * @param mixed $args
     * @return $this
     */
    public function setDataUsingMethod($key, $args = [])
    {
        $this->dataUsingMethod[$key] = $args;
        return $this;
    }

    /**
     * Get backend type (getter for testing)
     *
     * @return string|null
     */
    public function getBackendType()
    {
        return $this->getData('backend_type');
    }

    /**
     * Set backend type (setter for testing)
     *
     * @param string $value
     * @return $this
     */
    public function setBackendType($value)
    {
        $this->setData('backend_type', $value);
        return $this;
    }

    /**
     * Get frontend input (getter for testing)
     *
     * @return string|null
     */
    public function getFrontendInput()
    {
        return $this->getData('frontend_input');
    }

    /**
     * Set frontend input (setter for testing)
     *
     * @param string $value
     * @return $this
     */
    public function setFrontendInput($value)
    {
        $this->setData('frontend_input', $value);
        return $this;
    }

    /**
     * Set scope global (custom setter for testing)
     *
     * @param bool $value
     * @return $this
     */
    public function setScopeGlobal($value)
    {
        $this->setData('is_global', $value ? 1 : 0);
        return $this;
    }
}
