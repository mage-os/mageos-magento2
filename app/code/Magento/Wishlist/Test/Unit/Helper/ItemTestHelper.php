<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Wishlist\Test\Unit\Helper;

use Magento\Wishlist\Model\Item;

/**
 * Test helper for Magento\Catalog\Model\Product\Configuration\Item\ItemInterface
 * 
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class ItemTestHelper extends Item
{
    /**
     * @var array
     */
    private $data = [];

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
     * Wakeup method
     *
     * @return void
     */
    public function __wakeup()
    {
        // No-op for testing
    }

    /**
     * Get option by code
     *
     * @param string $code
     * @return mixed
     */
    public function getOptionByCode($code)
    {
        // Support callback pattern used in Bundle tests
        if (isset($this->data['option_by_code_callback']) && is_callable($this->data['option_by_code_callback'])) {
            return call_user_func($this->data['option_by_code_callback'], $code);
        }

        return $this->data['options'][$code] ?? $this->data['option_by_code_callback'] ?? null;
    }

    /**
     * Set option by code for testing
     * Supports both individual options and callback patterns
     *
     * @param string|callable|null $codeOrCallback
     * @param mixed $option
     * @return self
     */
    public function setOptionByCode($codeOrCallback, $option = null): self
    {
        // If only one parameter is provided, it's either a callback or null
        if (func_num_args() === 1) {
            $this->data['option_by_code_callback'] = $codeOrCallback;
        } else {
            // Two parameters: traditional code => option mapping
            $this->data['options'][$codeOrCallback] = $option;
        }
        return $this;
    }

    /**
     * Override load for test-specific behavior
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
     * Get wishlist ID
     *
     * @return int
     */
    public function getWishlistId()
    {
        return $this->wishlistId;
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
     * Get product ID
     *
     * @return int
     */
    public function getProductId()
    {
        return $this->productId;
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
     * Get buy request
     *
     * @return mixed
     */
    public function getBuyRequest()
    {
        return $this->buyRequest;
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
     * Override addToCart for test-specific behavior
     *
     * @param mixed $cart
     * @param bool $delete
     * @return bool
     */
    public function addToCart($cart, $delete = false)
    {
        if ($this->throwLocalizedException) {
            throw new \Magento\Framework\Exception\LocalizedException(__('message'));
        }
        return true;
    }

    /**
     * Override getProduct for test-specific behavior
     *
     * @return mixed
     */
    public function getProduct()
    {
        return $this->product;
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
     * Get item
     *
     * @return int
     */
    public function getItem()
    {
        return 2;
    }

    /**
     * Override save for test-specific behavior
     *
     * @return $this
     */
    public function save()
    {
        return $this;
    }
}
