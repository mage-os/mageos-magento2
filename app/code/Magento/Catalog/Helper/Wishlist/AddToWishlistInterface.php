<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Catalog\Helper\Wishlist;

/**
 * The "add to wish list" operations that Catalog blocks and templates use.
 *
 * Magento_Catalog binds this to a null implementation. Magento_Wishlist
 * binds it to Magento\Wishlist\Helper\Data, so with Wishlist installed
 * callers receive the wishlist helper itself.
 *
 * @api
 */
interface AddToWishlistInterface
{
    /**
     * Check whether customers can add products to their wish list.
     *
     * @return bool
     */
    public function isAllow();

    /**
     * Retrieve the post data for adding a product to the wish list.
     *
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\DataObject $item
     * @param array $params
     * @return string
     */
    public function getAddParams($item, array $params = []);
}
