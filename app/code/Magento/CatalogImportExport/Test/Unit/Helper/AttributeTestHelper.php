<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogImportExport\Test\Unit\Helper;

use Magento\Eav\Model\Entity\Attribute;

class AttributeTestHelper extends Attribute
{
    /**
     * @var bool
     */
    private $isVisible = false;

    /**
     * @var bool
     */
    private $isGlobal = false;

    /**
     * @var bool
     */
    private $isRequired = false;

    /**
     * @var bool
     */
    private $isUnique = false;

    /**
     * @var string
     */
    private $frontendLabel = '';

    /**
     * @var array
     */
    private $applyTo = [];

    /**
     * @var string
     */
    private $defaultValue = '';

    /**
     * @var bool
     */
    private $usesSource = false;

    /**
     * @var mixed
     */
    private $id = null;

    /**
     * @var string|null
     */
    private $attributeCode = null;

    /**
     * @var string|null
     */
    private $frontendInput = null;

    /**
     * @var bool
     */
    private $isStatic = false;

    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    public function getAttributeCode()
    {
        return $this->attributeCode;
    }

    /**
     * @param mixed $code
     * @return $this
     */
    public function setAttributeCode($code)
    {
        $this->attributeCode = $code;
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getIsRequired()
    {
        return $this->isRequired;
    }

    public function getIsUnique()
    {
        return $this->isUnique;
    }

    public function isStatic()
    {
        return $this->isStatic;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function setIsStatic($value)
    {
        $this->isStatic = $value;
        return $this;
    }

    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    public function usesSource()
    {
        return $this->usesSource;
    }

    public function getFrontendInput()
    {
        return $this->frontendInput;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function setFrontendInput($value)
    {
        $this->frontendInput = $value;
        return $this;
    }

    public function getIsVisible()
    {
        return $this->isVisible;
    }

    public function getApplyTo()
    {
        return $this->applyTo;
    }

    public function getIsGlobal()
    {
        return $this->isGlobal;
    }

    public function getFrontendLabel()
    {
        return $this->frontendLabel;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function setIsVisible($value)
    {
        $this->isVisible = $value;
        return $this;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function setIsGlobal($value)
    {
        $this->isGlobal = $value;
        return $this;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function setIsRequired($value)
    {
        $this->isRequired = $value;
        return $this;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function setIsUnique($value)
    {
        $this->isUnique = $value;
        return $this;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function setFrontendLabel($value)
    {
        $this->frontendLabel = $value;
        return $this;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function setApplyTo($value)
    {
        $this->applyTo = $value;
        return $this;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function setDefaultValue($value)
    {
        $this->defaultValue = $value;
        return $this;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function setUsesSource($value)
    {
        $this->usesSource = $value;
        return $this;
    }
}

