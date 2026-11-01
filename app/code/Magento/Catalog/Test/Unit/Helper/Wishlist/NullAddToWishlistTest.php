<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper\Wishlist;

use Magento\Catalog\Helper\Wishlist\NullAddToWishlist;
use Magento\Catalog\Model\Product;
use PHPUnit\Framework\TestCase;

class NullAddToWishlistTest extends TestCase
{
    /**
     * Without Magento_Wishlist, adding to the wish list is never allowed
     *
     * @return void
     */
    public function testAddingToWishlistIsNeverAllowed(): void
    {
        $nullAddToWishlist = new NullAddToWishlist();

        $this->assertFalse($nullAddToWishlist->isAllow());
        $this->assertSame('', $nullAddToWishlist->getAddParams($this->createStub(Product::class), ['foo' => 'bar']));
    }
}
