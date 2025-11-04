<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Test\Unit\Helper;

use Magento\Framework\Data\Form;

/**
 * Test helper for Form with custom methods
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class FormTestHelper extends Form
{
    /**
     * @var array
     */
    private $elements = [];

    /**
     * @var mixed
     */
    private $fieldset = null;

    /**
     * Constructor that skips parent dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Set HTML ID prefix (custom method for tests)
     *
     * @param string $prefix
     * @return $this
     */
    public function setHtmlIdPrefix($prefix)
    {
        return $this;
    }

    /**
     * Set form
     *
     * @param mixed $form
     * @return $this
     */
    public function setForm($form)
    {
        return $this;
    }

    /**
     * Set parent
     *
     * @param mixed $parent
     * @return $this
     */
    public function setParent($parent)
    {
        return $this;
    }

    /**
     * Set base URL
     *
     * @param string $url
     * @return $this
     */
    public function setBaseUrl($url)
    {
        return $this;
    }

    /**
     * Add fieldset
     *
     * @param string $elementId
     * @param array $config
     * @param bool $after
     * @param bool $isAdvanced
     * @return mixed
     */
    public function addFieldset($elementId, $config = [], $after = false, $isAdvanced = false)
    {
        return $this->fieldset;
    }

    /**
     * Set fieldset for testing
     *
     * @param mixed $fieldset
     * @return $this
     */
    public function setFieldset($fieldset): self
    {
        $this->fieldset = $fieldset;
        return $this;
    }

    /**
     * Set values
     *
     * @param array $values
     * @return $this
     */
    public function setValues($values)
    {
        return $this;
    }

    /**
     * Get element
     *
     * @param string $elementId
     * @return mixed
     */
    public function getElement($elementId)
    {
        return $this->elements[$elementId] ?? null;
    }

    /**
     * Set element for testing
     *
     * @param string $elementId
     * @param mixed $element
     * @return $this
     */
    public function setElement($elementId, $element): self
    {
        $this->elements[$elementId] = $element;
        return $this;
    }
}
