<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Pricing\Test\Unit\Helper;

use Magento\Framework\Pricing\Render;

/**
 * Test helper for Pricing Render
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class RenderTestHelper extends Render
{
    /**
     * @var mixed
     */
    private $productItem = null;

    /**
     * @var string
     */
    private $renderResult = '';

    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    public function getProductItem()
    {
        return $this->productItem;
    }

    public function setProductItem($value)
    {
        $this->productItem = $value;
        return $this;
    }

    public function render($priceType, $saleableItem, array $arguments = [])
    {
        return $this->renderResult;
    }

    public function setRenderResult($value)
    {
        $this->renderResult = $value;
        return $this;
    }
}

