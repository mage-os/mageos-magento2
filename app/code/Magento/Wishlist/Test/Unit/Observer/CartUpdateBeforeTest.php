<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);


namespace Magento\Wishlist\Test\Unit\Observer;

use Magento\Checkout\Model\Cart;
use Magento\Checkout\Model\Session;
use Magento\Framework\DataObject;
use Magento\Framework\Event;
use Magento\Framework\Message\ManagerInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item;
use Magento\Wishlist\Helper\Data;
use Magento\Wishlist\Model\Wishlist;
use Magento\Wishlist\Model\WishlistFactory;
use Magento\Wishlist\Observer\CartUpdateBefore as Observer;
use Magento\Framework\TestFramework\Unit\Helper\MockCreationTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CartUpdateBeforeTest extends TestCase
{
    use MockCreationTrait;

    /**
     * @var Observer
     */
    protected $observer;

    /**
     * @var Data|MockObject
     */
    protected $helper;

    /**
     * @var Session|MockObject
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Customer\Model\Session|MockObject
     */
    protected $customerSession;

    /**
     * @var WishlistFactory|MockObject
     */
    protected $wishlistFactory;

    /**
     * @var Wishlist|MockObject
     */
    protected $wishlist;

    /**
     * @var ManagerInterface|MockObject
     */
    protected $messageManager;

    protected function setUp(): void
    {
        $this->helper = $this->createMock(Data::class);
        $this->wishlistFactory = $this->createPartialMock(WishlistFactory::class, ['create']);
        $this->wishlist = $this->createMock(Wishlist::class);
        $this->wishlistFactory->expects($this->any())
            ->method('create')
            ->willReturn($this->wishlist);

        $this->observer = new Observer(
            $this->helper,
            $this->wishlistFactory
        );
    }

    /**
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testExecute()
    {
        $customerId = 1;
        $itemId = 5;
        $itemQty = 123;
        $productId = 321;

        $eventObserver = $this->createMock(\Magento\Framework\Event\Observer::class);

        $event = $this->createPartialMock(Event::class, []);
        $reflection = new \ReflectionClass($event);
        $property = $reflection->getProperty('_data');
        $property->setValue($event, []);

        $eventObserver->expects($this->exactly(2))
            ->method('getEvent')
            ->willReturn($event);

        $quoteItem = $this->createPartialMockWithReflection(Item::class, ['getProductId', 'getBuyRequest']);

        $buyRequest = $this->createPartialMock(DataObject::class, []);
        $reflection = new \ReflectionClass($buyRequest);
        $property = $reflection->getProperty('_data');
        $property->setValue($buyRequest, []);

        $infoData = $this->createPartialMock(DataObject::class, ['toArray']);

        $infoData->expects($this->once())
            ->method('toArray')
            ->willReturn([$itemId => ['qty' => $itemQty, 'wishlist' => true]]);

        $cart = $this->createMock(Cart::class);
        $quote = $this->createPartialMockWithReflection(
            Quote::class,
            ['getCustomerId', 'getItemById', 'removeItem']
        );

        $event->setCart($cart);
        $event->setInfo($infoData);

        $cart->expects($this->any())
            ->method('getQuote')
            ->willReturn($quote);

        $quoteItem->method('getProductId')->willReturn($productId);
        $quoteItem->method('getBuyRequest')->willReturn($buyRequest);

        $buyRequest->setQty($itemQty);

        $quote->method('getCustomerId')->willReturn($customerId);
        $quote->method('getItemById')->with($itemId)->willReturn($quoteItem);

        $this->wishlist->expects($this->once())
            ->method('loadByCustomerId')
            ->with($this->logicalOr($customerId, true))
            ->willReturnSelf();

        $this->wishlist->expects($this->once())
            ->method('addNewItem')
            ->with($this->logicalOr($productId, $buyRequest));

        $this->wishlist->expects($this->once())
            ->method('save');

        $this->helper->expects($this->once())
            ->method('calculate');

        /** @var $eventObserver \Magento\Framework\Event\Observer */
        $this->assertSame(
            $this->observer,
            $this->observer->execute($eventObserver)
        );
    }
}
