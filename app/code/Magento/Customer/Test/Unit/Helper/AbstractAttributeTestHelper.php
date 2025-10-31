<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Helper;

use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;

/**
 * Test helper for AbstractAttribute with custom methods
 */
class AbstractAttributeTestHelper extends AbstractAttribute
{
    /**
     * @var array<string, mixed>
     */
    private array $testData = [];

    /**
     * Constructor that skips parent to avoid dependency injection
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get ID
     *
     * @return int|string|null
     */
    public function getId()
    {
        return $this->testData['id'] ?? null;
    }

    /**
     * Set ID
     *
     * @param mixed $id
     * @return $this
     */
    public function setId($id)
    {
        $this->testData['id'] = $id;
        return $this;
    }

    /**
     * Get attribute code
     *
     * @return string|null
     */
    public function getAttributeCode()
    {
        return $this->testData['attribute_code'] ?? null;
    }

    /**
     * Set attribute code
     *
     * @param mixed $code
     * @return $this
     */
    public function setAttributeCode($code)
    {
        $this->testData['attribute_code'] = $code;
        return $this;
    }

    /**
     * Get data using method
     *
     * @param string $key
     * @param mixed $args
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getDataUsingMethod($key, $args = null)
    {
        return $key;
    }

    /**
     * Uses source
     *
     * @return bool
     */
    public function usesSource()
    {
        return $this->testData['uses_source'] ?? false;
    }

    /**
     * Set uses source
     *
     * @param mixed $usesSource
     * @return $this
     */
    public function setUsesSource($usesSource)
    {
        $this->testData['uses_source'] = $usesSource;
        return $this;
    }

    /**
     * Get frontend input
     *
     * @return string|null
     */
    public function getFrontendInput()
    {
        return $this->testData['frontend_input'] ?? null;
    }

    /**
     * Set frontend input
     *
     * @param mixed $input
     * @return $this
     */
    public function setFrontendInput($input)
    {
        $this->testData['frontend_input'] = $input;
        return $this;
    }

    /**
     * Get source
     *
     * @return mixed
     */
    public function getSource()
    {
        return $this->testData['source'] ?? null;
    }

    /**
     * Set source
     *
     * @param mixed $source
     * @return $this
     */
    public function setSource($source): self
    {
        $this->testData['source'] = $source;
        return $this;
    }

    /**
     * Get is user defined
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsUserDefined()
    {
        return $this->testData['is_user_defined'] ?? false;
    }

    /**
     * Set is user defined
     *
     * @param mixed $isUserDefined
     * @return $this
     */
    public function setIsUserDefined($isUserDefined)
    {
        $this->testData['is_user_defined'] = $isUserDefined;
        return $this;
    }

    /**
     * Get entity type
     *
     * @return mixed
     */
    public function getEntityType()
    {
        return $this->testData['entity_type'] ?? null;
    }

    /**
     * Set entity type
     *
     * @param mixed $entityType
     * @return $this
     */
    public function setEntityType($entityType)
    {
        $this->testData['entity_type'] = $entityType;
        return $this;
    }

    /**
     * Get is visible (custom method)
     *
     * @return bool|null
     */
    public function getIsVisible()
    {
        return $this->testData['is_visible'] ?? null;
    }

    /**
     * Set is visible
     *
     * @param mixed $isVisible
     * @return $this
     */
    public function setIsVisible($isVisible)
    {
        $this->testData['is_visible'] = $isVisible;
        return $this;
    }

    /**
     * Get used in forms (custom method)
     *
     * @return array
     */
    public function getUsedInForms()
    {
        return $this->testData['used_in_forms'] ?? [];
    }

    /**
     * Set used in forms
     *
     * @param mixed $forms
     * @return $this
     */
    public function setUsedInForms($forms)
    {
        $this->testData['used_in_forms'] = $forms;
        return $this;
    }

    /**
     * Get label (custom method)
     *
     * @return string|null
     */
    public function getLabel()
    {
        return $this->testData['label'] ?? null;
    }

    /**
     * Set label
     *
     * @param mixed $label
     * @return $this
     */
    public function setLabel($label)
    {
        $this->testData['label'] = $label;
        return $this;
    }
}
