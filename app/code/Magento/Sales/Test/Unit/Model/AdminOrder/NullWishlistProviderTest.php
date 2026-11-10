<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Sales\Test\Unit\Model\AdminOrder;

use Magento\Sales\Model\AdminOrder\NullWishlistProvider;
use PHPUnit\Framework\TestCase;

class NullWishlistProviderTest extends TestCase
{
    /**
     * Without Magento_Wishlist there are no wish lists or wish list items
     *
     * @return void
     */
    public function testProvidesNothing(): void
    {
        $provider = new NullWishlistProvider();

        $this->assertNull($provider->getCustomerWishlist(1));
        $this->assertNull($provider->getWishlist(1));
        $this->assertNull($provider->getWishlistItem(1, 'info_buyRequest'));
    }
}
