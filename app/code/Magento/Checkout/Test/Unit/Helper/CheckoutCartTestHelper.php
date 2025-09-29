<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Checkout\Test\Unit\Helper;

use Magento\Checkout\Model\Cart as CheckoutCart;

/**
 * Test helper class for Checkout Cart used across Checkout and related module tests
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class CheckoutCartTestHelper extends CheckoutCart
{
    /**
     * @var mixed
     */
    public $quote = null;

    /**
     * @var array
     */
    public array $items = [];

    /**
     * Constructor - skip parent constructor to avoid dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get quote
     *
     * @return mixed
     */
    public function getQuote()
    {
        return $this->quote;
    }

    /**
     * Set quote
     *
     * @param mixed $quote
     * @return $this
     */
    public function setQuote($quote): self
    {
        $this->quote = $quote;
        return $this;
    }

    /**
     * Get items
     *
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * Set items
     *
     * @param array $items
     * @return $this
     */
    public function setItems(array $items): self
    {
        $this->items = $items;
        return $this;
    }

    /**
     * Add product
     *
     * @param mixed $productInfo
     * @param mixed $requestInfo
     * @return $this
     */
    public function addProduct($productInfo, $requestInfo = null): self
    {
        // No-op for testing
        return $this;
    }

    /**
     * Save
     *
     * @return $this
     */
    public function save(): self
    {
        // No-op for testing
        return $this;
    }

    /**
     * Get should redirect to cart
     *
     * @return bool
     */
    public function shouldRedirectToCart(): bool
    {
        return false;
    }

    /**
     * Get cart URL
     *
     * @return string
     */
    public function getCartUrl(): string
    {
        return 'cart_url';
    }
}
