<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);


namespace Magento\Wishlist\Test\Unit\Observer;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Event;
use Magento\Framework\Message\ManagerInterface;
use Magento\Wishlist\Helper\Data;
use Magento\Wishlist\Model\ResourceModel\Wishlist\Collection;
use Magento\Wishlist\Model\Wishlist;
use Magento\Wishlist\Model\WishlistFactory;
use Magento\Wishlist\Observer\AddToCart as Observer;
use Magento\Framework\Test\Unit\Helper\EventTestHelper;
use Magento\Framework\Test\Unit\Helper\ResponseInterfaceTestHelper;
use Magento\Checkout\Test\Unit\Helper\SessionTestHelper as CheckoutSessionTestHelper;
use Magento\Customer\Test\Unit\Helper\SessionTestHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AddToCartTest extends TestCase
{
    /**
     * @var Observer
     */
    protected $observer;

    /**
     * @var array
     */
    protected $mocks;

    protected function setUp(): void
    {
        $this->mocks = [
            'checkoutSession' => $this->createCheckoutSessionMock(),
            'customerSession' => $this->createCustomerSessionMock(),
            'wishlistFactory' => $this->createPartialMock(WishlistFactory::class, ['create']),
            'wishlist' => $this->createMock(Wishlist::class),
            'messageManager' => $this->createPartialMock(\Magento\Framework\Message\Manager::class, ['addError'])
        ];

        $this->mocks['wishlistFactory']->method('create')->willReturn($this->mocks['wishlist']);

        $this->observer = new Observer(
            $this->mocks['checkoutSession'],
            $this->mocks['customerSession'],
            $this->mocks['wishlistFactory'],
            $this->mocks['messageManager']
        );
    }

    public function testExecute()
    {
        $wishlistId = 1;
        $customerId = 2;
        $url = 'http://some.pending/url';
        $message = 'some error msg';

        $eventObserver = $this->createMock(\Magento\Framework\Event\Observer::class);
        
        $event = $this->createEventMock();
        $request = $this->createMock(RequestInterface::class);
        $response = $this->createResponseMock();
        
        $wishlists = $this->createMock(Collection::class);
        $loadedWishlist = $this->createPartialMock(Wishlist::class, ['getId', 'delete']);

        $eventObserver->expects($this->any())->method('getEvent')->willReturn($event);

        $request->expects($this->any())->method('getParam')->with('wishlist_next')->willReturn(true);
        
        $event->request = $request;
        $event->response = $response;

        $this->mocks['checkoutSession']->wishlistPendingMessages = [$message];
        $this->mocks['checkoutSession']->wishlistPendingUrls = [$url];
        $this->mocks['checkoutSession']->singleWishlistId = $wishlistId;

        $this->mocks['customerSession']->loggedIn = true;
        $this->mocks['customerSession']->customerId = $customerId;
        $this->mocks['wishlist']->expects($this->once())
            ->method('loadByCustomerId')
            ->with($this->logicalOr($customerId, true))
            ->willReturnSelf();
        $this->mocks['wishlist']->expects($this->once())
            ->method('getItemCollection')
            ->willReturn($wishlists);
        $loadedWishlist->expects($this->once())
            ->method('getId')
            ->willReturn($wishlistId);
        $loadedWishlist->expects($this->once())
            ->method('delete');
        $wishlists->expects($this->once())
            ->method('load')
            ->willReturn([$loadedWishlist]);
        $this->mocks['messageManager']->expects($this->once())
            ->method('addError')
            ->with($message)
            ->willReturnSelf();

        /** @var $eventObserver \Magento\Framework\Event\Observer */
        $this->observer->execute($eventObserver);
    }

    private function createEventMock()
    {
        return new EventTestHelper();
    }

    /**
     * Create response mock
     */
    private function createResponseMock()
    {
        return new ResponseInterfaceTestHelper();
    }

    private function createCheckoutSessionMock()
    {
        return new CheckoutSessionTestHelper();
    }

    private function createCustomerSessionMock()
    {
        return new SessionTestHelper();
    }
}
