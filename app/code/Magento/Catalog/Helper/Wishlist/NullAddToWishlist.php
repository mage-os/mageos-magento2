<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Catalog\Helper\Wishlist;

/**
 * Used when Magento_Wishlist is not installed: adding to the wish list is never allowed.
 */
class NullAddToWishlist implements AddToWishlistInterface
{
    /**
     * @inheritdoc
     */
    public function isAllow()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function getAddParams($item, array $params = [])
    {
        return '';
    }
}
