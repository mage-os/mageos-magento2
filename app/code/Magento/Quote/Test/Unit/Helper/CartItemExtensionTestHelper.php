<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

use Magento\Quote\Api\Data\CartItemExtensionInterface;

/**
 * Test helper for CartItemExtension
 *
 * This helper implements CartItemExtensionInterface to provide test-specific functionality.
 * Used across 11 test files to set cart item extension attributes.
 *
 * Provides:
 * - negotiableQuoteItem (used in 10 files) - NegotiableQuoteItem data for B2B quotes
 *
 * All other CartItemExtensionInterface methods return null by default.
 */
class CartItemExtensionTestHelper implements CartItemExtensionInterface
{
    /**
     * @var mixed
     */
    private $negotiableQuoteItem;

    /**
     * Get negotiable quote item
     *
     * @return mixed
     */
    public function getNegotiableQuoteItem()
    {
        return $this->negotiableQuoteItem;
    }

    /**
     * Set negotiable quote item
     *
     * @param mixed $negotiableQuoteItem
     * @return $this
     */
    public function setNegotiableQuoteItem($negotiableQuoteItem)
    {
        $this->negotiableQuoteItem = $negotiableQuoteItem;
        return $this;
    }
}
