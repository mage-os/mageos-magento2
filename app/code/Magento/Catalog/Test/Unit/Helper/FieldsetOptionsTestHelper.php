<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Block\Adminhtml\Product\Composite\Fieldset\Options;

class FieldsetOptionsTestHelper extends Options
{
    public function __construct()
    {
        // Empty constructor to avoid parent constructor dependencies
    }

    /**
     * @param mixed $value
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setSkipJsReloadPrice($value)
    {
        return $this;
    }

    /**
     * @param mixed $option
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setOption($option)
    {
        return $this;
    }
}

