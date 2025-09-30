<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Test\Unit\Helper;

/**
 * Test helper for Fieldset
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class FieldsetTestHelper
{
    /**
     * Add field
     *
     * @param string $elementId
     * @param string $type
     * @param array $config
     * @param mixed $after
     * @return ElementTestHelper
     */
    public function addField($elementId, $type, $config, $after = false)
    {
        return new ElementTestHelper();
    }
}
