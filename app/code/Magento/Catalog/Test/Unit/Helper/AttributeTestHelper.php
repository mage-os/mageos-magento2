<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Model\ResourceModel\Eav\Attribute;

/**
 * TestHelper for Attribute with dynamic methods
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class AttributeTestHelper extends Attribute
{
    /** @var string|null */
    private $frontendLabel = null;
    /** @var array */
    private $attributesByCode = [];
    /** @var array */
    private $dataUsingMethod = [];
    /** @var string|null */
    private $attributeCode = null;
    /** @var bool */
    private $isScopeGlobal = false;
    /** @var string|null */
    private $backendType = null;
    /** @var string|null */
    private $frontendInput = null;

    public function __construct()
    {
        // Skip parent constructor to avoid complex dependencies
    }

    // Dynamic methods from addMethods
    public function getFrontendLabel()
    {
        return $this->frontendLabel;
    }

    public function setFrontendLabel($value)
    {
        $this->frontendLabel = $value;
        return $this;
    }

    public function getAttributesByCode()
    {
        return $this->attributesByCode;
    }

    public function setAttributesByCode($value)
    {
        $this->attributesByCode = $value;
        return $this;
    }

    public function getDataUsingMethod($method, $args = [])
    {
        return isset($this->dataUsingMethod[$method]) ? $this->dataUsingMethod[$method] : null;
    }

    public function setDataUsingMethod($key, $args = [])
    {
        $this->dataUsingMethod[$key] = $args;
        return $this;
    }

    public function getAttributeCode()
    {
        return $this->attributeCode;
    }

    public function setAttributeCode($value)
    {
        $this->attributeCode = $value;
        return $this;
    }

    public function getIsScopeGlobal()
    {
        return $this->isScopeGlobal;
    }

    public function setIsScopeGlobal($value)
    {
        $this->isScopeGlobal = $value;
        return $this;
    }

    public function getBackendType()
    {
        return $this->backendType;
    }

    public function setBackendType($value)
    {
        $this->backendType = $value;
        return $this;
    }

    public function getFrontendInput()
    {
        return $this->frontendInput;
    }

    public function setFrontendInput($value)
    {
        $this->frontendInput = $value;
        return $this;
    }
}
