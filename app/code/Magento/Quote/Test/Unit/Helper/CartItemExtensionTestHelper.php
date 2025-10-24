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
 * This helper implements CartItemExtensionInterface to provide
 * test-specific functionality for cart item extension attributes.
 */
class CartItemExtensionTestHelper implements CartItemExtensionInterface
{
    /**
     * @var mixed
     */
    private $negotiableQuoteItem;

    /**
     * @var mixed
     */
    private $discounts;

    /**
     * @var \Magento\User\Api\Data\UserInterface|null
     */
    private $quoteItemTestAttribute;

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

    /**
     * Get discounts
     *
     * @return mixed
     */
    public function getDiscounts()
    {
        return $this->discounts;
    }

    /**
     * Set discounts
     *
     * @param mixed $discounts
     * @return $this
     */
    public function setDiscounts($discounts)
    {
        $this->discounts = $discounts;
        return $this;
    }

    /**
     * Get quote item test attribute
     *
     * @return \Magento\User\Api\Data\UserInterface|null
     */
    public function getQuoteItemTestAttribute()
    {
        return $this->quoteItemTestAttribute;
    }

    /**
     * Set quote item test attribute
     *
     * @param \Magento\User\Api\Data\UserInterface|null $quoteItemTestAttribute
     * @return $this
     */
    public function setQuoteItemTestAttribute($quoteItemTestAttribute)
    {
        $this->quoteItemTestAttribute = $quoteItemTestAttribute;
        return $this;
    }
}
