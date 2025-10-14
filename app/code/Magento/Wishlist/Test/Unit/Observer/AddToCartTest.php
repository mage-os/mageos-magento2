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
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Event;
use Magento\Framework\Message\ManagerInterface;
use Magento\Wishlist\Helper\Data;
use Magento\Wishlist\Model\ResourceModel\Wishlist\Collection;
use Magento\Wishlist\Model\Wishlist;
use Magento\Wishlist\Model\WishlistFactory;
use Magento\Wishlist\Observer\AddToCart as Observer;
use Magento\Framework\App\Test\Unit\Helper\ResponseInterfaceTestHelper;
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
            'checkoutSession' => $this->createPartialMock(Session::class, []),
            'customerSession' => $this->createCustomerSessionMock(),
            'wishlistFactory' => $this->createPartialMock(WishlistFactory::class, ['create']),
            'wishlist' => $this->createMock(Wishlist::class),
            'messageManager' => $this->createPartialMock(\Magento\Framework\Message\Manager::class, ['addError'])
        ];

        // Initialize storage for magic __call methods
        $reflection = new \ReflectionClass($this->mocks['checkoutSession']);
        $property = $reflection->getProperty('storage');
        $property->setValue($this->mocks['checkoutSession'], new \Magento\Framework\Session\Storage());

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
        
        $event->setRequest($request);
        $event->setResponse($response);

        $this->mocks['checkoutSession']->setWishlistPendingMessages([$message]);
        $this->mocks['checkoutSession']->setWishlistPendingUrls([$url]);
        $this->mocks['checkoutSession']->setSingleWishlistId($wishlistId);

        // Mock customer session methods
        $this->mocks['customerSession']->method('isLoggedIn')->willReturn(true);
        $this->mocks['customerSession']->method('getCustomerId')->willReturn($customerId);
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
        $event = $this->createPartialMock(Event::class, []);
        $reflection = new \ReflectionClass($event);
        $property = $reflection->getProperty('_data');
        $property->setValue($event, []);
        return $event;
    }

    private function createResponseMock()
    {
        return new ResponseInterfaceTestHelper();
    }

    private function createCustomerSessionMock()
    {
        $session = $this->createPartialMock(CustomerSession::class, ['isLoggedIn', 'getCustomerId']);
        
        // Initialize storage for magic __call methods
        $reflection = new \ReflectionClass($session);
        $property = $reflection->getProperty('storage');
        $property->setValue($session, new \Magento\Framework\Session\Storage());
        
        return $session;
    }
}
