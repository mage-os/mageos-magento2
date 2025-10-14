<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

use Magento\Quote\Api\Data\CartItemExtension;

/**
 * Test helper for Magento\Quote\Api\Data\CartItemExtension
 *
 * WHY THIS HELPER IS REQUIRED:
 * - CartItemExtension is dynamically generated extension attributes interface
 * - Methods like getNegotiableQuoteItem/setNegotiableQuoteItem are added dynamically
 * - Cannot use createPartialMock on dynamically generated methods
 * - Provides explicit implementation for testing negotiable quote item functionality
 *
 * Used By: Multiple NegotiableQuote test files
 */
class CartItemExtensionTestHelper extends CartItemExtension
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
