<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Test\Unit\Helper;

use Magento\Framework\Data\Form\Element\Text;

/**
 * Test helper for Text form element
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class TextTestHelper extends Text
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Set value
     *
     * @param mixed $value
     * @return $this
     */
    public function setValue($value): self
    {
        return $this;
    }

    /**
     * Set is checked
     *
     * @param mixed $checked
     * @return $this
     */
    public function setIsChecked($checked): self
    {
        return $this;
    }
}
