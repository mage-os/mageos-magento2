<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Wishlist\Model\Sales\AdminOrder;

use Magento\Sales\Model\AdminOrder\WishlistProviderInterface;
use Magento\Wishlist\Model\Item;
use Magento\Wishlist\Model\ItemFactory;
use Magento\Wishlist\Model\Wishlist;
use Magento\Wishlist\Model\WishlistFactory;

/**
 * Provides wish lists and wish list items to the admin order create model
 */
class WishlistProvider implements WishlistProviderInterface
{
    /**
     * @var WishlistFactory
     */
    private $wishlistFactory;

    /**
     * @var ItemFactory
     */
    private $itemFactory;

    /**
     * @param WishlistFactory $wishlistFactory
     * @param ItemFactory $itemFactory
     */
    public function __construct(WishlistFactory $wishlistFactory, ItemFactory $itemFactory)
    {
        $this->wishlistFactory = $wishlistFactory;
        $this->itemFactory = $itemFactory;
    }

    /**
     * @inheritdoc
     *
     * @return Wishlist
     */
    public function getCustomerWishlist(?int $customerId)
    {
        return $this->wishlistFactory->create()->loadByCustomerId($customerId, true);
    }

    /**
     * @inheritdoc
     *
     * @return Wishlist
     */
    public function getWishlist(int $wishlistId)
    {
        return $this->wishlistFactory->create()->load($wishlistId);
    }

    /**
     * @inheritdoc
     *
     * @return Item
     */
    public function getWishlistItem(int $itemId, $optionsFilter = null)
    {
        return $this->itemFactory->create()->loadWithOptions($itemId, $optionsFilter);
    }
}
