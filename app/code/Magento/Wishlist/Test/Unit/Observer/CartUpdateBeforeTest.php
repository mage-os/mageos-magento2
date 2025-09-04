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
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CartUpdateBeforeTest extends TestCase
{
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
        $this->helper = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->wishlistFactory = $this->getMockBuilder(WishlistFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['create'])
            ->getMock();
        $this->wishlist = $this->getMockBuilder(Wishlist::class)
            ->disableOriginalConstructor()
            ->getMock();
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

        $eventObserver = $this->getMockBuilder(\Magento\Framework\Event\Observer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $event = new class extends Event {
            /**
             * @var Cart
             */
            public $cart;
            /**
             * @var DataObject
             */
            public $info;
            
            public function __construct()
            {
            }
            
            public function getCart()
            {
                return $this->cart;
            }
            
            public function getInfo()
            {
                return $this->info;
            }
        };

        $eventObserver->expects($this->exactly(2))
            ->method('getEvent')
            ->willReturn($event);

        $quoteItem = new class extends Item {
            /**
             * @var int
             */
            public $productId;
            /**
             * @var DataObject
             */
            public $buyRequest;
            
            public function __construct()
            {
            }
            
            public function getProductId()
            {
                return $this->productId;
            }
            
            public function getBuyRequest()
            {
                return $this->buyRequest;
            }
            
            public function __wakeup()
            {
            }
        };

        $buyRequest = new class extends DataObject {
            /**
             * @var int
             */
            public $qty;
            
            public function __construct()
            {
            }
            
            public function setQty($qty)
            {
                $this->qty = $qty;
                $_ = [$qty];
                unset($_);
                return $this;
            }
        };

        $infoData = $this->getMockBuilder(DataObject::class)
            ->onlyMethods(['toArray'])
            ->disableOriginalConstructor()
            ->getMock();

        $infoData->expects($this->once())
            ->method('toArray')
            ->willReturn([$itemId => ['qty' => $itemQty, 'wishlist' => true]]);

        $cart = $this->getMockBuilder(Cart::class)
            ->disableOriginalConstructor()
            ->getMock();
        $quote = new class extends Quote {
            /**
             * @var int
             */
            public $customerId;
            /**
             * @var array
             */
            public $items = [];
            
            public function __construct()
            {
            }
            
            public function getCustomerId()
            {
                return $this->customerId;
            }
            
            public function getItemById($itemId)
            {
                return $this->items[$itemId] ?? null;
            }
            
            public function removeItem($itemId)
            {
                unset($this->items[$itemId]);
                return $this;
            }
            
            public function __wakeup()
            {
            }
        };

        $event->cart = $cart;
        $event->info = $infoData;

        $cart->expects($this->any())
            ->method('getQuote')
            ->willReturn($quote);

        $quoteItem->productId = $productId;
        $quoteItem->buyRequest = $buyRequest;

        $buyRequest->setQty($itemQty);

        $quote->customerId = $customerId;
        $quote->items[$itemId] = $quoteItem;

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
