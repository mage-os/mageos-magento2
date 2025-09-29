<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Wishlist\Test\Unit\Helper;

use Magento\Wishlist\Model\Wishlist;

/**
 * Test helper for Wishlist class in ItemCarrier tests
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class WishlistCarrierTestHelper extends Wishlist
{
    /**
     * @var string
     */
    private $sharingCode;

    /**
     * @var bool
     */
    private $isOwner;

    /**
     * @var int
     */
    private $wishlistId;

    /**
     * @var mixed
     */
    private $itemCollection;

    /**
     * Constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Set sharing code
     *
     * @param string $sharingCode
     * @return $this
     */
    public function setSharingCode($sharingCode)
    {
        $this->sharingCode = $sharingCode;
        return $this;
    }

    /**
     * Get sharing code
     *
     * @return string
     */
    public function getSharingCode()
    {
        return $this->sharingCode;
    }

    /**
     * Set is owner
     *
     * @param bool $isOwner
     * @return $this
     */
    public function setIsOwner($isOwner)
    {
        $this->isOwner = $isOwner;
        return $this;
    }

    /**
     * Is owner
     *
     * @param int $customerId
     * @return bool
     */
    public function isOwner($customerId)
    {
        return $this->isOwner;
    }

    /**
     * Set ID
     *
     * @param int $wishlistId
     * @return $this
     */
    public function setId($wishlistId)
    {
        $this->wishlistId = $wishlistId;
        return $this;
    }

    /**
     * Get ID
     *
     * @return int
     */
    public function getId()
    {
        return $this->wishlistId;
    }

    /**
     * Set item collection
     *
     * @param mixed $collection
     * @return $this
     */
    public function setItemCollection($collection)
    {
        $this->itemCollection = $collection;
        return $this;
    }

    /**
     * Get item collection
     *
     * @return mixed
     */
    public function getItemCollection()
    {
        return $this->itemCollection;
    }

    /**
     * Save
     *
     * @return $this
     */
    public function save()
    {
        return $this;
    }
}
