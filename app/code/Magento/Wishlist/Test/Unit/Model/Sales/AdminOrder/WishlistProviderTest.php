<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Wishlist\Test\Unit\Model\Sales\AdminOrder;

use Magento\Wishlist\Model\Item;
use Magento\Wishlist\Model\ItemFactory;
use Magento\Wishlist\Model\Sales\AdminOrder\WishlistProvider;
use Magento\Wishlist\Model\Wishlist;
use Magento\Wishlist\Model\WishlistFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class WishlistProviderTest extends TestCase
{
    /**
     * @var Wishlist|MockObject
     */
    private $wishlist;

    /**
     * @var Item|MockObject
     */
    private $item;

    /**
     * @var WishlistProvider
     */
    private $provider;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->wishlist = $this->createMock(Wishlist::class);
        $this->item = $this->createMock(Item::class);
        $wishlistFactory = $this->createMock(WishlistFactory::class);
        $wishlistFactory->method('create')->willReturn($this->wishlist);
        $itemFactory = $this->createMock(ItemFactory::class);
        $itemFactory->method('create')->willReturn($this->item);
        $this->provider = new WishlistProvider($wishlistFactory, $itemFactory);
    }

    /**
     * The customer's wish list is loaded and created when missing
     *
     * @return void
     */
    public function testGetCustomerWishlist(): void
    {
        $this->wishlist->expects($this->once())
            ->method('loadByCustomerId')
            ->with(3, true)
            ->willReturnSelf();

        $this->assertSame($this->wishlist, $this->provider->getCustomerWishlist(3));
    }

    /**
     * Without a customer ID the wish list is neither loaded nor created
     *
     * @return void
     */
    public function testGetCustomerWishlistWithoutCustomer(): void
    {
        $this->wishlist->expects($this->once())
            ->method('loadByCustomerId')
            ->with(null, true)
            ->willReturnSelf();

        $this->assertSame($this->wishlist, $this->provider->getCustomerWishlist(null));
    }

    /**
     * A wish list is loaded by its ID
     *
     * @return void
     */
    public function testGetWishlist(): void
    {
        $this->wishlist->expects($this->once())
            ->method('load')
            ->with(7)
            ->willReturnSelf();

        $this->assertSame($this->wishlist, $this->provider->getWishlist(7));
    }

    /**
     * A wish list item is loaded with the requested options
     *
     * @return void
     */
    public function testGetWishlistItem(): void
    {
        $this->item->expects($this->once())
            ->method('loadWithOptions')
            ->with(9, 'info_buyRequest')
            ->willReturnSelf();

        $this->assertSame($this->item, $this->provider->getWishlistItem(9, 'info_buyRequest'));
    }
}
