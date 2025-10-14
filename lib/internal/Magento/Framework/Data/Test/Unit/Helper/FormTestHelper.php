<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Data\Test\Unit\Helper;

use Magento\Framework\Data\Form;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class FormTestHelper extends Form
{
    /**
     * @var mixed
     */
    private $element;

    public function __construct()
    {
        $this->_allElements = [];
        $this->_elementsIndex = [
            'stores' => new ElementTestHelper(),
            'position' => new ElementTestHelper(),
            'is_active' => new ElementTestHelper()
        ];
    }

    public function addFieldset($elementId, $config, $after = false, $isAdvanced = false)
    {
        return new FieldsetTestHelper();
    }

    public function setElement($element)
    {
        $this->element = $element;
    }
}
