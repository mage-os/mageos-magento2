<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Data\Test\Unit\Helper;

use Magento\Framework\Data\Form\Element\Fieldset;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class FieldsetTestHelper extends Fieldset
{
    public function __construct()
    {
    }

    public function addField($elementId, $type, $config, $after = false, $isAdvanced = false)
    {
        return new ElementTestHelper();
    }
}
