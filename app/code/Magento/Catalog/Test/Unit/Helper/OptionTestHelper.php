<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Options\Option;

class OptionTestHelper extends Option
{
    public function __construct()
    {
        // Empty constructor
    }

    /**
     * @param mixed $ignoreCaching
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setIgnoreCaching($ignoreCaching)
    {
        return $this;
    }

    /**
     * @param mixed $product
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setProduct($product)
    {
        return $this;
    }

    /**
     * @return array
     */
    public function getOptionValues()
    {
        return [];
    }
}

