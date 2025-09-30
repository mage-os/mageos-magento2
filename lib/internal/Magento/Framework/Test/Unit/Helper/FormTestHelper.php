<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Test\Unit\Helper;

use Magento\Framework\Data\Form;

/**
 * Test helper for Form
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class FormTestHelper extends Form
{
    /**
     * @var mixed
     */
    private $element;

    /**
     * @var array
     */
    private $fieldset = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get form
     *
     * @return $this
     */
    public function getForm()
    {
        return $this;
    }

    /**
     * Add fieldset
     *
     * @param string $elementId
     * @param array $config
     * @param mixed $after
     * @param mixed $isAdvanced
     * @return FieldsetTestHelper
     */
    public function addFieldset($elementId, $config, $after = false, $isAdvanced = false)
    {
        $fieldsetMock = new FieldsetTestHelper();
        $this->fieldset[$elementId] = $fieldsetMock;
        return $fieldsetMock;
    }

    /**
     * Get fieldset
     *
     * @param string $elementId
     * @return mixed
     */
    public function getFieldset($elementId)
    {
        return $this->fieldset[$elementId] ?? null;
    }

    /**
     * Add field
     *
     * @param string $elementId
     * @param string $type
     * @param array $config
     * @param mixed $after
     * @return $this
     */
    public function addField($elementId, $type, $config, $after = false)
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
        if (in_array($elementId, ['stores', 'position', 'is_active'])) {
            return new ElementTestHelper();
        }
        return null;
    }

    /**
     * Set element
     *
     * @param mixed $element
     * @return void
     */
    public function setElement($element)
    {
        $this->element = $element;
    }

    /**
     * Set values
     *
     * @param mixed $values
     * @return $this
     */
    public function setValues($values)
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
     * Set renderer
     *
     * @param mixed $renderer
     * @return $this
     */
    public function setRenderer($renderer)
    {
        return $this;
    }
}
