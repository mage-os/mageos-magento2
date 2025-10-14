<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Api\Data\ProductRender\ButtonInterface;
use Magento\Catalog\Api\Data\ProductRenderExtensionInterface;

/**
 * Test helper class for ProductRenderExtensionInterface used across Catalog and related module tests
 */
class ProductRenderExtensionInterfaceTestHelper implements ProductRenderExtensionInterface
{
    /** @var mixed */
    private $wishlistButton = null;
    /** @var mixed */
    private $reviewHtml = null;

    public function __construct()
    {
    }

    public function getWishlistButton()
    {
        return $this->wishlistButton;
    }

    public function setWishlistButton(\Magento\Catalog\Api\Data\ProductRender\ButtonInterface $wishlistButton)
    {
        $this->wishlistButton = $wishlistButton;
        return $this;
    }

    public function getReviewHtml()
    {
        return $this->reviewHtml;
    }

    public function setReviewHtml($reviewHtml)
    {
        $this->reviewHtml = $reviewHtml;
        return $this;
    }
}
