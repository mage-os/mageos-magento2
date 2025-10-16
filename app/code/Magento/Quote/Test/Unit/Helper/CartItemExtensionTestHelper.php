<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

use Magento\Quote\Api\Data\CartItemExtensionInterface;

/**
 * Test helper for CartItemExtension to support dynamic getNegotiableQuoteItem/setNegotiableQuoteItem methods
 */
class CartItemExtensionTestHelper implements CartItemExtensionInterface
{
    /**
     * @var \Magento\NegotiableQuote\Api\Data\NegotiableQuoteItemInterface|null
     */
    private $negotiableQuoteItem;

    /**
     * Get negotiable quote item
     *
     * @return \Magento\NegotiableQuote\Api\Data\NegotiableQuoteItemInterface|null
     */
    public function getNegotiableQuoteItem()
    {
        return $this->negotiableQuoteItem;
    }

    /**
     * Set negotiable quote item
     *
     * @param \Magento\NegotiableQuote\Api\Data\NegotiableQuoteItemInterface|null $negotiableQuoteItem
     * @return $this
     */
    public function setNegotiableQuoteItem($negotiableQuoteItem)
    {
        $this->negotiableQuoteItem = $negotiableQuoteItem;
        return $this;
    }
}
