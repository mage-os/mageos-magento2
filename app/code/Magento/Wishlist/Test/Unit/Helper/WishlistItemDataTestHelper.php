<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Wishlist\Test\Unit\Helper;

use Magento\Wishlist\Model\Item;

class WishlistItemDataTestHelper extends Item
{
    /**
     * @var int
     */
    private $wishlistItemId = 1;

    /**
     * @var mixed
     */
    private $productId = null;

    /**
     * @var mixed
     */
    private $qty = null;

    /**
     * @var int
     */
    private $id = 1;

    /**
     * @var mixed
     */
    private $product = null;

    public function __construct()
    {
    }

    public function getWishlistItemId()
    {
        return $this->wishlistItemId;
    }

    public function getProductId()
    {
        return $this->productId;
    }

    public function getQty()
    {
        return $this->qty;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getProduct()
    {
        return $this->product;
    }

    public function setWishlistItemId($id)
    {
        $this->wishlistItemId = $id;
        return $this;
    }

    public function setProductId($id)
    {
        $this->productId = $id;
        return $this;
    }

    public function setQty($qty)
    {
        $this->qty = $qty;
        return $this;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function setProduct($product)
    {
        $this->product = $product;
        return $this;
    }
}
