<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Data\Test\Unit\Helper;

use Magento\Framework\Data\Form\Element\Checkbox;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class ElementTestHelper extends Checkbox
{
    public function __construct()
    {
    }

    public function setRenderer($renderer)
    {
        return $this;
    }
}
