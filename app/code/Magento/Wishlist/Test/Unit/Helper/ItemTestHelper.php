<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Wishlist\Test\Unit\Helper;

use Magento\Wishlist\Model\Item;

/**
 * Test helper class for Wishlist Item used across Wishlist and related module tests
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class ItemTestHelper extends Item
{
    /**
     * @var int
     */
    public $productId = 1;
    /**
     * @var mixed
     */
    public $buyRequest = null;
    /**
     * @var mixed
     */
    public $product = null;
    /**
     * @var int
     */
    private $id = 2;
    /**
     * @var int
     */
    private $wishlistId = 1;
    /**
     * @var bool
     */
    public $throwLocalizedException = false;

    /**
     * Constructor - skip parent constructor to avoid dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Load item by ID
     *
     * @param mixed $id
     * @param mixed $field
     * @return $this
     */
    public function load($id, $field = null)
    {
        return $this;
    }

    /**
     * Get item ID
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get wishlist ID
     *
     * @return int
     */
    public function getWishlistId()
    {
        return $this->wishlistId;
    }

    /**
     * Get product ID
     *
     * @return int
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * Get buy request
     *
     * @return mixed
     */
    public function getBuyRequest()
    {
        return $this->buyRequest;
    }

    /**
     * Merge buy request
     *
     * @param mixed $buyRequest
     * @return $this
     */
    public function mergeBuyRequest($buyRequest)
    {
        $this->buyRequest = $buyRequest;
        return $this;
    }

    /**
     * Add to cart
     *
     * @param mixed $cart
     * @param bool $delete
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addToCart($cart, $delete = false)
    {
        if ($this->throwLocalizedException) {
            throw new \Magento\Framework\Exception\LocalizedException(__('message'));
        }
        return true;
    }

    /**
     * Get product
     *
     * @return mixed
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set options
     *
     * @param mixed $options
     * @return $this
     */
    public function setOptions($options)
    {
        return $this;
    }

    /**
     * Set product mock
     *
     * @param mixed $mock
     * @return $this
     */
    public function setProductMock($mock)
    {
        $this->product = $mock;
        return $this;
    }

    /**
     * Set buy request mock
     *
     * @param mixed $mock
     * @return $this
     */
    public function setBuyRequestMock($mock)
    {
        $this->buyRequest = $mock;
        return $this;
    }

    /**
     * Set product ID
     *
     * @param mixed $productId
     * @return $this
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;
        return $this;
    }

    /**
     * Set wishlist ID
     *
     * @param mixed $wishlistId
     * @return $this
     */
    public function setWishlistId($wishlistId)
    {
        $this->wishlistId = $wishlistId;
        return $this;
    }

    /**
     * Set store ID
     *
     * @param mixed $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        return $this;
    }

    /**
     * Set item
     *
     * @param mixed $item
     * @return $this
     */
    public function getItem()
    {
        return 2;
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

    /**
     * Wakeup method
     *
     * @return void
     */
    public function __wakeup()
    {
        // No-op for testing
    }
}
