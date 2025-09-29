<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Wishlist\Test\Unit\Helper;

use Magento\Wishlist\Model\Item;

/**
 * Test helper for Item class in ItemCarrier tests
 */
class ItemCarrierTestHelper extends Item
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var mixed
     */
    public $product;

    /**
     * @var mixed
     */
    private $originalProduct;

    /**
     * @var bool
     */
    public $shouldThrowException = false;

    /**
     * @var \Exception
     */
    public $exception;

    /**
     * Constructor
     *
     * @param int|null $id
     * @param mixed $product
     */
    public function __construct($id = null, $product = null)
    {
        $this->id = $id;
        $this->product = $product;
    }

    /**
     * Set ID
     *
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get ID
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get product
     *
     * @return mixed
     */
    public function getProduct()
    {
        return $this->originalProduct ?: $this->product;
    }

    /**
     * Set original product
     *
     * @param mixed $product
     * @return $this
     */
    public function setOriginalProduct($product)
    {
        $this->originalProduct = $product;
        $this->product = $product;
        return $this;
    }

    /**
     * Set product
     *
     * @param mixed $product
     * @return $this
     */
    public function setProduct($product)
    {
        $this->product = $product;
        return $this;
    }

    /**
     * Get product URL
     *
     * @return string
     */
    public function getProductUrl()
    {
        return 'http://example.com/product';
    }

    /**
     * Set product
     *
     * @param mixed $product
     * @return $this
     */
    public function setProductMock($product)
    {
        $this->product = $product;
        return $this;
    }

    /**
     * Unset product
     *
     * @return $this
     */
    public function unsetProduct()
    {
        return $this;
    }

    /**
     * Set quantity
     *
     * @param mixed $qty
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setQty($qty)
    {
        return $this;
    }

    /**
     * Add to cart
     *
     * @param mixed $cart
     * @param bool $delete
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addToCart($cart, $delete = false)
    {
        if ($this->shouldThrowException && $this->exception) {
            throw $this->exception;
        }
        
        if ($this->product && $this->product->getDisableAddToCart()) {
            return false;
        }
        return true;
    }

    /**
     * Delete
     *
     * @return $this
     */
    public function delete()
    {
        return $this;
    }

    /**
     * Unset product (alias)
     *
     * @return $this
     */
    public function unsProduct()
    {
        return $this->unsetProduct();
    }
}
