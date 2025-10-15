<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Model\ResourceModel\Eav\Attribute;

/**
 * Test helper for Catalog EAV Attribute
 *
 * Extends Attribute to add custom methods for testing
 */
class AttributeTestHelper extends Attribute
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * Constructor - skip parent to avoid dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get attribute group code
     *
     * @return string|null
     */
    public function getAttributeGroupCode()
    {
        return $this->data['attribute_group_code'] ?? null;
    }

    /**
     * Set attribute group code
     *
     * @param string $code
     * @return $this
     */
    public function setAttributeGroupCode($code)
    {
        $this->data['attribute_group_code'] = $code;
        return $this;
    }

    /**
     * Get apply to
     *
     * @return array
     */
    public function getApplyTo()
    {
        return $this->data['apply_to'] ?? [];
    }

    /**
     * Set apply to
     *
     * @param array $applyTo
     * @return $this
     */
    public function setApplyTo($applyTo)
    {
        $this->data['apply_to'] = $applyTo;
        return $this;
    }

    /**
     * Get frontend input
     *
     * @return string|null
     */
    public function getFrontendInput()
    {
        return $this->data['frontend_input'] ?? null;
    }

    /**
     * Set frontend input
     *
     * @param string $input
     * @return $this
     */
    public function setFrontendInput($input)
    {
        $this->data['frontend_input'] = $input;
        return $this;
    }

    /**
     * Get attribute code
     *
     * @return string|null
     */
    public function getAttributeCode()
    {
        return $this->data['attribute_code'] ?? null;
    }

    /**
     * Set attribute code
     *
     * @param string $code
     * @return $this
     */
    public function setAttributeCode($code)
    {
        $this->data['attribute_code'] = $code;
        return $this;
    }

    /**
     * Get uses source
     *
     * @return bool
     */
    public function usesSource()
    {
        return $this->data['uses_source'] ?? false;
    }

    /**
     * Set uses source
     *
     * @param bool $usesSource
     * @return $this
     */
    public function setUsesSource($usesSource)
    {
        $this->data['uses_source'] = $usesSource;
        return $this;
    }

    /**
     * Get source
     *
     * @return mixed
     */
    public function getSource()
    {
        return $this->data['source'] ?? null;
    }

    /**
     * Set source
     *
     * @param mixed $source
     * @return $this
     */
    public function setSource($source)
    {
        $this->data['source'] = $source;
        return $this;
    }

    /**
     * Get value
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->data['value'] ?? null;
    }

    /**
     * Set value
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
     * Get is required
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsRequired()
    {
        return $this->data['is_required'] ?? false;
    }

    /**
     * Set is required
     *
     * @param bool $isRequired
     * @return $this
     */
    public function setIsRequired($isRequired)
    {
        $this->data['is_required'] = $isRequired;
        return $this;
    }

    /**
     * Get default value
     *
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->data['default_value'] ?? null;
    }

    /**
     * Set default value
     *
     * @param mixed $defaultValue
     * @return $this
     */
    public function setDefaultValue($defaultValue)
    {
        $this->data['default_value'] = $defaultValue;
        return $this;
    }
}
