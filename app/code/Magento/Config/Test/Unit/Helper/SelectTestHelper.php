<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Config\Test\Unit\Helper;

use Magento\Framework\Data\Form\Element\Select;

/**
 * Test helper for Select
 */
class SelectTestHelper extends Select
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
     * setDisabled (custom method for testing)
     *
     * @param mixed $value
     * @return $this
     */
    public function setDisabled($value)
    {
        $this->data['disabled'] = $value;
        return $this;
    }
}
