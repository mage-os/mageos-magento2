<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Framework\Test\Unit\Helper;

use Magento\Framework\Data\Form\Element\Checkbox;

/**
 * Test helper for Checkbox with custom methods
 * 
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class CheckboxTestHelper extends Checkbox
{
    /**
     * Constructor that skips parent dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Set checked
     *
     * @param bool $checked
     * @return $this
     */
    public function setChecked($checked)
    {
        return $this;
    }

    /**
     * Set value
     *
     * @param mixed $value
     * @return $this
     */
    public function setValue($value)
    {
        return $this;
    }
}
