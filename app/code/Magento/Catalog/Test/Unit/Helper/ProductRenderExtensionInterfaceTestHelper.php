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
    /**
     * @var ButtonInterface|null
     */
    private $wishlistButton = null;

    /**
     * @var string|null
     */
    private $reviewHtml = null;

    /**
     * Constructor - skip parent constructor to avoid dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get review HTML
     *
     * @return string|null
     */
    public function getReviewHtml()
    {
        return $this->reviewHtml;
    }

    /**
     * Set review HTML
     *
     * @param string $reviewHtml
     * @return $this
     */
    public function setReviewHtml($reviewHtml)
    {
        $this->reviewHtml = $reviewHtml;
        return $this;
    }

    /**
     * Get wishlist button
     *
     * @return \Magento\Catalog\Api\Data\ProductRender\ButtonInterface|null
     */
    public function getWishlistButton()
    {
        return $this->wishlistButton;
    }

    /**
     * Set wishlist button
     *
     * @param \Magento\Catalog\Api\Data\ProductRender\ButtonInterface $wishlistButton
     * @return $this
     */
    public function setWishlistButton(\Magento\Catalog\Api\Data\ProductRender\ButtonInterface $wishlistButton)
    {
        $this->wishlistButton = $wishlistButton;
        return $this;
    }

    /**
     * Get extension attributes values.
     *
     * @return \Magento\Framework\Api\ExtensionAttributesInterface|null
     */
    public function getExtensionAttributes()
    {
        return null;
    }

    /**
     * Set extension attributes object.
     *
     * @param \Magento\Framework\Api\ExtensionAttributesInterface $extensionAttributes
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setExtensionAttributes(\Magento\Framework\Api\ExtensionAttributesInterface $extensionAttributes)
    {
        // No-op for testing
    }
}
