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
    public int $wishlistItemId = 1;

    public function __construct()
    {
    }

    public function getWishlistItemId()
    {
        return $this->wishlistItemId;
    }

    public function setWishlistItemId($id)
    {
        $this->wishlistItemId = $id;
        return $this;
    }
}
