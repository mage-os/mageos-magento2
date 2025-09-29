<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Wishlist\Test\Unit\Helper;

use Magento\Wishlist\Model\Item;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class ItemUpdateTestHelper extends Item
{
    /**
     * @var mixed
     */
    private $id;

    /**
     * @var mixed
     */
    private $wishlistId;

    /**
     * @var mixed
     */
    private $product;

    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    public function load($modelId, $field = null)
    {
        return $this;
    }

    public function setId($id)
    {
        $this->id = $id;
        $this->wishlistId = $id;
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getWishlistId()
    {
        return $this->wishlistId;
    }

    public function setQty($qty)
    {
        return $this;
    }

    public function save()
    {
        return $this;
    }

    public function getProduct()
    {
        return $this->product;
    }

    public function setProduct($product)
    {
        $this->product = $product;
        return $this;
    }
}
