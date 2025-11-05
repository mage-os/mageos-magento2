<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Config\Test\Unit\Helper;

use Magento\Framework\Data\Form\Element\Fieldset;

/**
 * Test helper for Fieldset
 */
class FieldsetTestHelper extends Fieldset
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
     * setOriginalData (custom method for testing)
     *
     * @param mixed $value
     * @return $this
     */
    public function setOriginalData($value)
    {
        $this->data['originalData'] = $value;
        return $this;
    }
}
