<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Block\Product\View\Options as ProductOptions;

class ProductOptionsTestHelper extends ProductOptions
{
    public function __construct()
    {
        // Empty constructor
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

    /**
     * @param \Magento\Catalog\Model\Product|null $product
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setProduct(?\Magento\Catalog\Model\Product $product = null)
    {
        return $this;
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        return '';
    }
}

