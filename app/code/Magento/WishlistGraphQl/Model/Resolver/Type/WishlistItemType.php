<?php
/**
 * Copyright 2020 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\WishlistGraphQl\Model\Resolver\Type;

/**
 * Resolving the wishlist item type
 *
 * Kept so that supportedTypes registered for this class keep working: Magento_WishlistGraphQl
 * makes it the preference for the CatalogGraphQl resolver, whose arguments it inherits.
 *
 * @deprecated Register supportedTypes on the replacement instead.
 * @see \Magento\CatalogGraphQl\Model\Resolver\Type\WishlistItemType
 */
class WishlistItemType extends \Magento\CatalogGraphQl\Model\Resolver\Type\WishlistItemType
{
}
