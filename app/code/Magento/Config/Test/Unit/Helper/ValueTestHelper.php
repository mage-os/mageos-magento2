<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Config\Test\Unit\Helper;

use Magento\Framework\App\Config\Value;

/**
 * Test helper for Value
 */
class ValueTestHelper extends Value
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * Skip parent constructor
     */
    public function __construct()
    {
        // Skip parent constructor
    }

    /**
     * getScope (custom method for testing)
     *
     * @return mixed
     */
    public function getScope()
    {
        return $this->data['scope'] ?? null;
    }

    /**
     * getPath (custom method for testing)
     *
     * @return mixed
     */
    public function getPath()
    {
        return $this->data['path'] ?? null;
    }

    /**
     * getValue (custom method for testing)
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->data['value'] ?? null;
    }

    /**
     * getScopeId (custom method for testing)
     *
     * @return mixed
     */
    public function getScopeId()
    {
        return $this->data['scopeId'] ?? null;
    }

    /**
     * setPath (custom method for testing)
     *
     * @param mixed $value
     * @return $this
     */
    public function setPath($value)
    {
        $this->data['path'] = $value;
        return $this;
    }

    /**
     * setScope (custom method for testing)
     *
     * @param mixed $value
     * @return $this
     */
    public function setScope($value)
    {
        $this->data['scope'] = $value;
        return $this;
    }

    /**
     * setScopeId (custom method for testing)
     *
     * @param mixed $value
     * @return $this
     */
    public function setScopeId($value)
    {
        $this->data['scopeId'] = $value;
        return $this;
    }

    /**
     * setValue (custom method for testing)
     *
     * @param mixed $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->data['value'] = $value;
        return $this;
    }

    /**
     * setField (custom method for testing)
     *
     * @param mixed $value
     * @return $this
     */
    public function setField($value)
    {
        $this->data['field'] = $value;
        return $this;
    }

    /**
     * setGroupId (custom method for testing)
     *
     * @param mixed $value
     * @return $this
     */
    public function setGroupId($value)
    {
        $this->data['groupId'] = $value;
        return $this;
    }

    /**
     * setFieldConfig (custom method for testing)
     *
     * @param mixed $value
     * @return $this
     */
    public function setFieldConfig($value)
    {
        $this->data['fieldConfig'] = $value;
        return $this;
    }

    /**
     * setScopeCode (custom method for testing)
     *
     * @param mixed $value
     * @return $this
     */
    public function setScopeCode($value)
    {
        $this->data['scopeCode'] = $value;
        return $this;
    }
}
