<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Api\Test\Unit\Helper;

use Magento\Framework\Api\CustomAttributesDataInterface;

/**
 * Test helper for CustomAttributesDataInterface
 */
class CustomAttributesDataInterfaceTestHelper implements CustomAttributesDataInterface
{
    /**
     * @var bool
     */
    private $isFilterable = false;

    /**
     * @var bool
     */
    private $isFilterableInSearch = false;

    /**
     * @var bool
     */
    private $isSearchable = false;

    /**
     * @var bool
     */
    private $isVisibleInAdvancedSearch = false;

    /**
     * @var string
     */
    private $backendType = 'varchar';

    /**
     * @var string
     */
    private $frontendInput = 'text';

    /**
     * @var bool
     */
    private $usesSource = false;

    /**
     * Get is filterable
     *
     * @return bool
     */
    public function getIsFilterable()
    {
        return $this->isFilterable;
    }

    /**
     * Set is filterable
     *
     * @param bool $value
     * @return $this
     */
    public function setIsFilterable($value)
    {
        $this->isFilterable = $value;
        return $this;
    }

    /**
     * Get is filterable in search
     *
     * @return bool
     */
    public function getIsFilterableInSearch()
    {
        return $this->isFilterableInSearch;
    }

    /**
     * Set is filterable in search
     *
     * @param bool $value
     * @return $this
     */
    public function setIsFilterableInSearch($value)
    {
        $this->isFilterableInSearch = $value;
        return $this;
    }

    /**
     * Get is searchable
     *
     * @return bool
     */
    public function getIsSearchable()
    {
        return $this->isSearchable;
    }

    /**
     * Set is searchable
     *
     * @param bool $value
     * @return $this
     */
    public function setIsSearchable($value)
    {
        $this->isSearchable = $value;
        return $this;
    }

    /**
     * Get is visible in advanced search
     *
     * @return bool
     */
    public function getIsVisibleInAdvancedSearch()
    {
        return $this->isVisibleInAdvancedSearch;
    }

    /**
     * Set is visible in advanced search
     *
     * @param bool $value
     * @return $this
     */
    public function setIsVisibleInAdvancedSearch($value)
    {
        $this->isVisibleInAdvancedSearch = $value;
        return $this;
    }

    /**
     * Get backend type
     *
     * @return string
     */
    public function getBackendType()
    {
        return $this->backendType;
    }

    /**
     * Set backend type
     *
     * @param string $value
     * @return $this
     */
    public function setBackendType($value)
    {
        $this->backendType = $value;
        return $this;
    }

    /**
     * Get frontend input
     *
     * @return string
     */
    public function getFrontendInput()
    {
        return $this->frontendInput;
    }

    /**
     * Set frontend input
     *
     * @param string $value
     * @return $this
     */
    public function setFrontendInput($value)
    {
        $this->frontendInput = $value;
        return $this;
    }

    /**
     * Uses source
     *
     * @return bool
     */
    public function usesSource()
    {
        return $this->usesSource;
    }

    /**
     * Set uses source
     *
     * @param bool $value
     * @return $this
     */
    public function setUsesSource($value)
    {
        $this->usesSource = $value;
        return $this;
    }

    /**
     * Get custom attributes
     *
     * @return array
     */
    public function getCustomAttributes()
    {
        return [];
    }

    /**
     * Set custom attributes
     *
     * @param array $customAttributes
     * @return $this
     */
    public function setCustomAttributes(array $customAttributes)
    {
        return $this;
    }

    /**
     * Get custom attribute
     *
     * @param string $attributeCode
     * @return mixed
     */
    public function getCustomAttribute($attributeCode)
    {
        return null;
    }

    /**
     * Set custom attribute
     *
     * @param string $attributeCode
     * @param mixed $attributeValue
     * @return $this
     */
    public function setCustomAttribute($attributeCode, $attributeValue)
    {
        return $this;
    }
}

