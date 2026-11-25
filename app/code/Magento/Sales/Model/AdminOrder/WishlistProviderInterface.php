<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Sales\Model\AdminOrder;

/**
 * Loads the wish lists and wish list items that the admin order create model works with
 *
 * Magento_Sales binds this to a null implementation. Magento_Wishlist binds it to an
 * implementation that returns Magento\Wishlist models.
 *
 * @api
 */
interface WishlistProviderInterface
{
    /**
     * Get the customer's wish list, creating it when the customer has none yet
     *
     * Without a customer ID an empty wish list is returned, and nothing is saved.
     *
     * @param int|null $customerId
     * @return \Magento\Framework\DataObject|null Null when wish lists are not available
     */
    public function getCustomerWishlist(?int $customerId);

    /**
     * Get a wish list by its ID
     *
     * @param int $wishlistId
     * @return \Magento\Framework\DataObject|null Null when wish lists are not available
     */
    public function getWishlist(int $wishlistId);

    /**
     * Get a wish list item with its options
     *
     * @param int $itemId
     * @param string|string[]|null $optionsFilter Codes of the options to load, null to load all of them
     * @return \Magento\Framework\DataObject|null Null when wish lists are not available
     */
    public function getWishlistItem(int $itemId, $optionsFilter = null);
}
