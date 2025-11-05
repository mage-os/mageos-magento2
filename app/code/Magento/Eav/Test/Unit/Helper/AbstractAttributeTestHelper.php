<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Eav\Test\Unit\Helper;

use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;

class AbstractAttributeTestHelper extends AbstractAttribute
{
    /**
     * @var mixed
     */
    private $maxValue = null;

    /**
     * @var mixed
     */
    private $backend = null;

    /**
     * @var string|null
     */
    private $storeLabel = null;

    /**
     * @var string|null
     */
    private $attributeCode = null;

    /**
     * @var mixed
     */
    private $isFilterable = null;

    /**
     * @var mixed
     */
    private $frontend = null;

    /**
     * @var mixed
     */
    private $isScopeWebsite = null;

    /**
     * @var mixed
     */
    private $isGlobal = null;

    /**
     * @var mixed
     */
    private $isScopeGlobal = null;

    /**
     * @var string|null
     */
    private $name = null;

    /**
     * @var mixed
     */
    private $attribute = null;

    /**
     * @var mixed
     */
    private $id = null;

    /**
     * @var mixed
     */
    private $entity = null;

    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    public function setMaxValue($value)
    {
        $this->maxValue = $value;
        return $this;
    }

    public function getMaxValue()
    {
        return $this->maxValue;
    }

    public function getBackend()
    {
        return $this->backend;
    }

    public function setBackend($value)
    {
        $this->backend = $value;
        return $this;
    }

    public function setStoreLabel($value)
    {
        $this->storeLabel = $value;
        return $this;
    }

    public function getStoreLabel()
    {
        return $this->storeLabel;
    }

    public function setAttributeCode($value)
    {
        $this->attributeCode = $value;
        return $this;
    }

    public function getAttributeCode()
    {
        return $this->attributeCode;
    }

    /**
     * @return mixed
     */
    public function getIsFilterable()
    {
        return $this->isFilterable;
    }

    /**
     * @param mixed $isFilterable
     * @return $this
     */
    public function setIsFilterable($isFilterable)
    {
        $this->isFilterable = $isFilterable;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFrontend()
    {
        return $this->frontend;
    }

    /**
     * @param mixed $frontend
     * @return $this
     */
    public function setFrontend($frontend)
    {
        $this->frontend = $frontend;
        return $this;
    }

    /**
     * @return mixed
     */
    public function isScopeWebsite()
    {
        return $this->isScopeWebsite;
    }

    /**
     * @param mixed $isScopeWebsite
     * @return $this
     */
    public function setIsScopeWebsite($isScopeWebsite)
    {
        $this->isScopeWebsite = $isScopeWebsite;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsGlobal()
    {
        return $this->isGlobal;
    }

    /**
     * @param mixed $isGlobal
     * @return $this
     */
    public function setIsGlobal($isGlobal)
    {
        $this->isGlobal = $isGlobal;
        return $this;
    }

    /**
     * @return mixed
     */
    public function isScopeGlobal()
    {
        return $this->isScopeGlobal;
    }

    /**
     * @param mixed $isScopeGlobal
     * @return $this
     */
    public function setIsScopeGlobal($isScopeGlobal)
    {
        $this->isScopeGlobal = $isScopeGlobal;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAttribute()
    {
        return $this->attribute ?: $this;
    }

    /**
     * @param mixed $attribute
     * @return $this
     */
    public function setAttribute($attribute)
    {
        $this->attribute = $attribute;
        return $this;
    }

    /**
     * @return mixed
     */
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

    /**
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param mixed $entity
     * @return $this
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
        return $this;
    }
}

