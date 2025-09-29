<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Checkout\Test\Unit\Helper;

use Magento\Checkout\Model\Session;

/**
 * Test helper class for Checkout Session used across Checkout and related module tests
 */
class SessionTestHelper extends Session
{
    /**
     * @var string
     */
    public string $sharedWishlist = '';

    /**
     * @var array
     */
    public array $wishlistPendingMessages = [];

    /**
     * @var array
     */
    public array $wishlistPendingUrls = [];

    /**
     * @var array
     */
    public array $wishlistIds = [];

    /**
     * @var mixed
     */
    public $quote = null;

    /**
     * @var int
     */
    public $singleWishlistId = 1;

    /**
     * @var bool
     */
    public bool $noCartRedirect = false;

    /**
     * Constructor - skip parent constructor to avoid dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get shared wishlist
     *
     * @return string
     */
    public function getSharedWishlist(): string
    {
        return $this->sharedWishlist;
    }

    /**
     * Set shared wishlist
     *
     * @param string $sharedWishlist
     * @return $this
     */
    public function setSharedWishlist(string $sharedWishlist): self
    {
        $this->sharedWishlist = $sharedWishlist;
        return $this;
    }

    /**
     * Get wishlist pending messages
     *
     * @return array
     */
    public function getWishlistPendingMessages(): array
    {
        return $this->wishlistPendingMessages;
    }

    /**
     * Set wishlist pending messages
     *
     * @param array $messages
     * @return $this
     */
    public function setWishlistPendingMessages(array $messages): self
    {
        $this->wishlistPendingMessages = $messages;
        return $this;
    }

    /**
     * Get wishlist pending URLs
     *
     * @return array
     */
    public function getWishlistPendingUrls(): array
    {
        return $this->wishlistPendingUrls;
    }

    /**
     * Set wishlist pending URLs
     *
     * @param array $urls
     * @return $this
     */
    public function setWishlistPendingUrls(array $urls): self
    {
        $this->wishlistPendingUrls = $urls;
        return $this;
    }

    /**
     * Get wishlist IDs
     *
     * @return array
     */
    public function getWishlistIds(): array
    {
        return $this->wishlistIds;
    }

    /**
     * Set wishlist IDs
     *
     * @param array $ids
     * @return $this
     */
    public function setWishlistIds(array $ids): self
    {
        $this->wishlistIds = $ids;
        return $this;
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
     * Get single wishlist ID
     *
     * @return int
     */
    public function getSingleWishlistId(): int
    {
        return $this->singleWishlistId;
    }

    /**
     * Set single wishlist ID
     *
     * @param mixed $id
     * @return $this
     */
    public function setSingleWishlistId($id): self
    {
        $this->singleWishlistId = $id;
        return $this;
    }

    /**
     * Set no cart redirect
     *
     * @param bool $redirect
     * @return $this
     */
    public function setNoCartRedirect(bool $redirect): self
    {
        $this->noCartRedirect = $redirect;
        return $this;
    }

    /**
     * Get no cart redirect
     *
     * @return bool
     */
    public function hasNoCartRedirect(): bool
    {
        return $this->noCartRedirect;
    }
}
