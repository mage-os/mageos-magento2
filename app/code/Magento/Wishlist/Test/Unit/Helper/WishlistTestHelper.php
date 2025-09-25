<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Wishlist\Test\Unit\Helper;

use Magento\Wishlist\Model\Wishlist;

/**
 * Test helper class for Wishlist used across Wishlist tests
 */
class WishlistTestHelper extends Wishlist
{
    /**
     * @var int
     */
    private int $id = 1;
    
    /**
     * @var int
     */
    private int $itemsCount = 0;
    
    /**
     * @var string
     */
    private string $sharingCode = 'test-sharing-code';
    
    /**
     * @var string
     */
    private string $updatedAt = '2024-01-01 00:00:00';

    /**
     * Constructor - skip parent constructor to avoid dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Load wishlist by customer ID
     *
     * @param int $customerId
     * @param bool $create
     * @return self
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function loadByCustomerId($customerId, $create = false): self
    {
        return $this;
    }

    /**
     * Get wishlist ID
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set wishlist ID
     *
     * @param mixed $id
     * @return self
     */
    public function setId($id): self
    {
        $this->id = (int) $id;
        return $this;
    }

    /**
     * Get items count
     *
     * @return int
     */
    public function getItemsCount(): int
    {
        return $this->itemsCount;
    }

    /**
     * Set items count
     *
     * @param int $itemsCount
     * @return self
     */
    public function setItemsCount(int $itemsCount): self
    {
        $this->itemsCount = $itemsCount;
        return $this;
    }

    /**
     * Get sharing code
     *
     * @return string
     */
    public function getSharingCode(): string
    {
        return $this->sharingCode;
    }

    /**
     * Set sharing code
     *
     * @param string $sharingCode
     * @return self
     */
    public function setSharingCode(string $sharingCode): self
    {
        $this->sharingCode = $sharingCode;
        return $this;
    }

    /**
     * Get updated at timestamp
     *
     * @return string
     */
    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }

    /**
     * Set updated at timestamp
     *
     * @param string $updatedAt
     * @return self
     */
    public function setUpdatedAt(string $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}
