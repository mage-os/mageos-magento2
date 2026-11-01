<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Sales\Model\AdminOrder;

/**
 * Used when Magento_Wishlist is not installed: there are no wish lists to load
 */
class NullWishlistProvider implements WishlistProviderInterface
{
    /**
     * @inheritdoc
     */
    public function getCustomerWishlist(?int $customerId)
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getWishlist(int $wishlistId)
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getWishlistItem(int $itemId, $optionsFilter = null)
    {
        return null;
    }
}
