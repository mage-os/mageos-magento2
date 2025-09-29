<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Helper;

use Magento\Customer\Model\Session;

/**
 * Test helper class for Customer Session used across Customer and related module tests
 */
class SessionTestHelper extends Session
{
    /**
     * @var bool
     */
    public bool $loggedIn = false;

    /**
     * @var int
     */
    public int $customerId = 1;

    /**
     * @var int
     */
    public int $wishlistItemCount = 0;

    /**
     * Constructor - skip parent constructor to avoid dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Check if customer is logged in
     *
     * @return bool
     */
    public function isLoggedIn(): bool
    {
        return $this->loggedIn;
    }

    /**
     * Set logged in status
     *
     * @param bool $loggedIn
     * @return $this
     */
    public function setIsLoggedIn(bool $loggedIn): self
    {
        $this->loggedIn = $loggedIn;
        return $this;
    }

    /**
     * Get customer ID
     *
     * @return int
     */
    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    /**
     * Set customer ID
     *
     * @param mixed $customerId
     * @return $this
     */
    public function setCustomerId($customerId): self
    {
        $this->customerId = (int)$customerId;
        return $this;
    }

    /**
     * Set wishlist item count
     *
     * @param int $count
     * @return $this
     */
    public function setWishlistItemCount(int $count): self
    {
        $this->wishlistItemCount = $count;
        return $this;
    }

    /**
     * Get wishlist item count
     *
     * @return int
     */
    public function getWishlistItemCount(): int
    {
        return $this->wishlistItemCount;
    }

    /**
     * @var mixed
     */
    private $customer = null;

    /**
     * Get customer
     *
     * @return mixed
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Set customer
     *
     * @param mixed $customer
     * @return $this
     */
    public function setCustomer($customer): self
    {
        $this->customer = $customer;
        return $this;
    }

    /**
     * Get data
     *
     * @param string $key
     * @param bool $clear
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getData($key = '', $clear = false)
    {
        return false;
    }
}
