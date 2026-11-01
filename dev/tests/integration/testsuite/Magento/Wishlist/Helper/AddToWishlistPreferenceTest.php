<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Wishlist\Helper;

use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Helper\Wishlist\AddToWishlistInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Checks that Magento_Wishlist replaces Catalog's null "add to wish list" implementation with its helper.
 */
class AddToWishlistPreferenceTest extends TestCase
{
    /**
     * @return void
     */
    public function testCatalogReceivesTheWishlistHelper(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $wishlistHelper = $objectManager->get(Data::class);

        $this->assertSame($wishlistHelper, $objectManager->get(AddToWishlistInterface::class));
        $this->assertSame($wishlistHelper, $objectManager->create(Context::class)->getWishlistHelper());
    }
}
