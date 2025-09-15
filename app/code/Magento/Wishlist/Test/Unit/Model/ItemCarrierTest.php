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

namespace Magento\Wishlist\Test\Unit\Model;

use Exception;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Exception as ProductException;
use Magento\Checkout\Helper\Cart as HelperCart;
use Magento\Checkout\Model\Cart;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Quote\Model\Quote;
use Magento\Wishlist\Helper\Data;
use Magento\Wishlist\Model\Item;
use Magento\Wishlist\Model\ItemCarrier;
use Magento\Wishlist\Model\LocaleQuantityProcessor;
use Magento\Wishlist\Model\ResourceModel\Item\Collection;
use Magento\Wishlist\Model\Wishlist;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class ItemCarrierTest extends TestCase
{
    /**
     * @var ItemCarrier
     */
    protected $model;

    /**
     * @var array
     */
    protected $mocks;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->mocks = [
            'session' => $this->createMock(Session::class),
            'quantityProcessor' => $this->createMock(LocaleQuantityProcessor::class),
            'cart' => $this->createMock(Cart::class),
            'logger' => $this->createMock(LoggerInterface::class),
            'wishlistHelper' => $this->createMock(Data::class),
            'cartHelper' => $this->createMock(HelperCart::class),
            'urlBuilder' => $this->createMock(UrlInterface::class),
            'manager' => $this->createPartialMock(
                \Magento\Framework\Message\Manager::class,
                ['addSuccessMessage', 'addErrorMessage']
            ),
            'redirect' => $this->createMock(RedirectInterface::class)
        ];

        $this->model = new ItemCarrier(
            $this->mocks['session'],
            $this->mocks['quantityProcessor'],
            $this->mocks['cart'],
            $this->mocks['logger'],
            $this->mocks['wishlistHelper'],
            $this->mocks['cartHelper'],
            $this->mocks['urlBuilder'],
            $this->mocks['manager'],
            $this->mocks['redirect']
        );
    }

    /**
     * @return void
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testMoveAllToCart(): void
    {
        $wishlistId = 7;
        $sessionCustomerId = 23;
        $itemOneId = 14;
        $itemTwoId = 17;
        $productOneName = 'product one';
        $productTwoName = 'product two';
        $qtys = [17 => 21];
        $isOwner = true;
        $indexUrl = 'index_url';
        $redirectUrl = 'redirect_url';
        $sharingCode = 'sharingcode';

        /** @var Item|MockObject $itemOneMock */
        $itemOneMock = $this->createMock(Item::class);
        /** @var Product|MockObject $productOneMock */
        $productOneMock = new class($productOneName) extends Product {
            /**
             * @var string
             */
            private $name;

            public function __construct($name)
            {
                $this->name = $name;
            }

            public function getName()
            {
                return $this->name;
            }
            public function getDisableAddToCart()
            {
                return true;
            }
            public function setDisableAddToCart($value)
            {
                return $this;
            }
        };

        /** @var Product|MockObject $productTwoMock */
        $productTwoMock = new class($productTwoName) extends Product {
            /**
             * @var string
             */
            private $name;

            public function __construct($name)
            {
                $this->name = $name;
            }

            public function getName()
            {
                return $this->name;
            }
            public function getDisableAddToCart()
            {
                return false;
            }
            public function setDisableAddToCart($value)
            {
                return $this;
            }
        };

        /** @var Item|MockObject $itemTwoMock */
        $itemTwoMock = new class($itemTwoId, $productTwoMock) extends Item {
            /**
             * @var int
             */
            private $id;
            /**
             * @var Product
             */
            private $product;

            public function __construct($id, $product)
            {
                $this->id = $id;
                $this->product = $product;
                $_ = [$id, $product];
                unset($_);
            }

            public function getId()
            {
                return $this->id;
            }
            public function getProduct()
            {
                return $this->product;
            }
            public function setQty($qty)
            {
                return $this;
            }
            public function addToCart($cart, $delete = false)
            {
                return true;
            }
            public function delete()
            {
                return $this;
            }
            public function getProductUrl()
            {
                return '';
            }
            public function unsProduct()
            {
                return $this;
            }
        };

        $itemOneMock->method('getProduct')->willReturn($productOneMock);
        $itemOneMock->method('getId')->willReturn($itemOneId);
        $itemOneMock->method('setQty')->willReturnSelf();
        $itemOneMock->method('addToCart')->willReturnSelf();
        $itemOneMock->method('delete')->willReturnSelf();
        $itemOneMock->method('getProductUrl')->willReturn('');

        $collection = [$itemTwoMock];

        /** @var Wishlist|MockObject $wishlistMock */
        $wishlistMock = new class($sharingCode, $isOwner, $wishlistId) extends Wishlist {
            /**
             * @var string
             */
            private $sharingCode;
            /**
             * @var bool
             */
            private $isOwner;
            /**
             * @var int
             */
            private $id;
            /**
             * @var Collection
             */
            private $itemCollection;

            public function __construct($sharingCode, $isOwner, $id)
            {
                $this->sharingCode = $sharingCode;
                $this->isOwner = $isOwner;
                $this->id = $id;
                $_ = [$sharingCode, $isOwner, $id];
                unset($_);
            }

            public function getSharingCode()
            {
                return $this->sharingCode;
            }

            public function isOwner($customerId)
            {
                return $this->isOwner;
            }

            public function getId()
            {
                return $this->id;
            }

            public function setItemCollection($collection)
            {
                $this->itemCollection = $collection;
                return $this;
            }

            public function getItemCollection()
            {
                return $this->itemCollection;
            }

            public function save()
            {
                return $this;
            }
        };

        $this->mocks['session']->expects($this->once())
            ->method('getCustomerId')
            ->willReturn($sessionCustomerId);

        /** @var Collection|MockObject $collectionMock */
        $collectionMock = $this->createMock(Collection::class);

        $wishlistMock->setItemCollection($collectionMock);

        $collectionMock->expects($this->once())
            ->method('setVisibilityFilter')
            ->with(true)
            ->willReturn($collection);

        $this->mocks['quantityProcessor']->expects($this->once())
            ->method('process')
            ->with($qtys[$itemTwoId])
            ->willReturnArgument(0);

        $this->mocks['wishlistHelper']->expects($this->once())
            ->method('getListUrl')
            ->with($wishlistId)
            ->willReturn($indexUrl);

        $this->mocks['cartHelper']->expects($this->once())
            ->method('getShouldRedirectToCart')
            ->with(null)
            ->willReturn(true);
        $this->mocks['cartHelper']->expects($this->once())
            ->method('getCartUrl')
            ->willReturn($redirectUrl);

        $this->mocks['manager']->expects($this->once())
            ->method('addSuccessMessage')
            ->with(__('%1 product(s) have been added to shopping cart: %2.', 1, '"' . $productTwoName . '"'), null)
            ->willReturnSelf();

        $this->mocks['cart']->expects($this->once())
            ->method('save')
            ->willReturnSelf();

        /** @var Quote|MockObject $collectionMock */
        $quoteMock = $this->createMock(Quote::class);

        $this->mocks['cart']->expects($this->once())
            ->method('getQuote')
            ->willReturn($quoteMock);

        $quoteMock->expects($this->once())
            ->method('collectTotals')
            ->willReturnSelf();

        $this->mocks['wishlistHelper']->expects($this->once())
            ->method('calculate')
            ->willReturnSelf();

        $this->assertEquals($redirectUrl, $this->model->moveAllToCart($wishlistMock, $qtys));
    }

    /**
     * @return void
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testMoveAllToCartWithNotSalableAndOptions(): void
    {
        $wishlistId = 7;
        $sessionCustomerId = 23;
        $itemOneId = 14;
        $itemTwoId = 17;
        $productOneName = 'product one';
        $productTwoName = 'product two';
        $qtys = [14 => 21, 17 => 29];
        $isOwner = false;
        $indexUrl = 'index_url';
        $redirectUrl = 'redirect_url';
        $sharingCode = 'sharingcode';

        /** @var Item|MockObject $itemOneMock */
        $itemOneMock = $this->createMock(Item::class);
        /** @var Item|MockObject $itemTwoMock */
        $itemTwoMock = $this->createMock(Item::class);

        /** @var Product|MockObject $productOneMock */
        $productOneMock = $this->createPartialMock(Product::class, [
            'getName'
        ]);
        /** @var Product|MockObject $productTwoMock */
        $productTwoMock = $this->createPartialMock(Product::class, [
            'getName'
        ]);

        $itemOneMock->expects($this->any())
            ->method('getProduct')
            ->willReturn($productOneMock);
        $itemTwoMock->expects($this->any())
            ->method('getProduct')
            ->willReturn($productTwoMock);

        $collection = [$itemOneMock, $itemTwoMock];

        /** @var Wishlist|MockObject $wishlistMock */
        $wishlistMock = new class($sharingCode, $isOwner, $wishlistId) extends Wishlist {
            /**
             * @var string
             */
            private $sharingCode;
            /**
             * @var bool
             */
            private $isOwner;
            /**
             * @var int
             */
            private $id;
            /**
             * @var Collection
             */
            private $itemCollection;

            public function __construct($sharingCode, $isOwner, $id)
            {
                $this->sharingCode = $sharingCode;
                $this->isOwner = $isOwner;
                $this->id = $id;
                $_ = [$sharingCode, $isOwner, $id];
                unset($_);
            }

            public function getSharingCode()
            {
                return $this->sharingCode;
            }

            public function isOwner($customerId)
            {
                return $this->isOwner;
            }

            public function getId()
            {
                return $this->id;
            }

            public function setItemCollection($collection)
            {
                $this->itemCollection = $collection;
                return $this;
            }

            public function getItemCollection()
            {
                return $this->itemCollection;
            }
        };

        $this->mocks['session']->expects($this->once())
            ->method('getCustomerId')
            ->willReturn($sessionCustomerId);

        /** @var Collection|MockObject $collectionMock */
        $collectionMock = $this->createMock(Collection::class);

        $wishlistMock->setItemCollection($collectionMock);

        $collectionMock->expects($this->once())
            ->method('setVisibilityFilter')
            ->with(true)
            ->willReturn($collection);

        $itemOneMock->expects($this->exactly(2))
            ->method('getId')
            ->willReturn($itemOneId);
        $itemTwoMock->expects($this->exactly(2))
            ->method('getId')
            ->willReturn($itemTwoId);

        $this->mocks['quantityProcessor']->expects($this->exactly(2))
            ->method('process')
            ->willReturnMap(
                [
                    [$qtys[$itemOneId], $qtys[$itemOneId]],
                    [$qtys[$itemTwoId], $qtys[$itemTwoId]]
                ]
            );
        $itemOneMock->expects($this->once())
            ->method('setQty')
            ->with($qtys[$itemOneId])
            ->willReturnSelf();
        $itemTwoMock->expects($this->once())
            ->method('setQty')
            ->with($qtys[$itemTwoId])
            ->willReturnSelf();

        $itemOneMock->expects($this->once())
            ->method('addToCart')
            ->with($this->mocks['cart'], $isOwner)
            ->willThrowException(new ProductException(__('Product Exception.')));
        $itemTwoMock->expects($this->once())
            ->method('addToCart')
            ->with($this->mocks['cart'], $isOwner)
            ->willThrowException(new LocalizedException(__('Localized Exception.')));

        /** @var Quote|MockObject $collectionMock */
        $quoteMock = $this->getMockBuilder(Quote::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->mocks['cart']->expects($this->exactly(4))
            ->method('getQuote')
            ->willReturn($quoteMock);

        /** @var Quote\Item|MockObject $collectionMock */
        $itemMock = $this->getMockBuilder(Quote\Item::class)
            ->disableOriginalConstructor()
            ->getMock();

        $quoteMock->expects($this->exactly(2))
            ->method('getItemByProduct')
            ->willReturn($itemMock);

        $quoteMock->expects($this->exactly(2))
            ->method('deleteItem')
            ->with($itemMock)
            ->willReturnSelf();

        $this->mocks['urlBuilder']->expects($this->once())
            ->method('getUrl')
            ->with('wishlist/shared', ['code' => $sharingCode])
            ->willReturn($indexUrl);

        $this->mocks['cartHelper']->expects($this->once())
            ->method('getShouldRedirectToCart')
            ->with(null)
            ->willReturn(false);

        $this->mocks['redirect']->expects($this->exactly(2))
            ->method('getRefererUrl')
            ->willReturn($redirectUrl);

        $productOneMock->expects($this->any())
            ->method('getName')
            ->willReturn($productOneName);
        $productTwoMock->expects($this->any())
            ->method('getName')
            ->willReturn($productTwoName);

        $this->mocks['manager']
            ->method('addErrorMessage')
            ->willReturnCallback(function ($arg1, $arg2) use ($productOneName, $productTwoName) {
                if ($arg1 == __('%1 for "%2".', 'Localized Exception', $productTwoName) && $arg2 === null) {
                    return $this->mocks['manager'];
                } elseif ($arg1 == __('We couldn\'t add the following product(s) to the shopping cart: %1.', '"' .
                        $productOneName . '"') && $arg2 === null) {
                    return $this->mocks['manager'];
                }
            });

        $this->mocks['wishlistHelper']->expects($this->once())
            ->method('calculate')
            ->willReturnSelf();

        $this->assertEquals($indexUrl, $this->model->moveAllToCart($wishlistMock, $qtys));
    }

    /**
     * @return void
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testMoveAllToCartWithException(): void
    {
        $wishlistId = 7;
        $sessionCustomerId = 23;
        $itemOneId = 14;
        $itemTwoId = 17;
        $productOneName = 'product one';
        $productTwoName = 'product two';
        $qtys = [14 => 21];
        $isOwner = true;
        $indexUrl = 'index_url';

        $itemOneMock = new class extends Item {
            /**
             * @var Product
             */
            public $product;
            /**
             * @var int
             */
            public $id;
            /**
             * @var int
             */
            public $qty;
            /**
             * @var string
             */
            public $productUrl = '';
            /**
             * @var Product
             */
            private $originalProduct;

            public function __construct()
            {
            }

            public function setOriginalProduct($product)
            {
                $this->originalProduct = $product;
                $this->product = $product;
            }

            public function getProduct()
            {
                // Always return the original product for testing purposes
                return $this->originalProduct;
            }

            public function getId()
            {
                return $this->id;
            }

            public function setQty($qty)
            {
                $this->qty = $qty;
                return $this;
            }

            public function addToCart($cart, $delete = false)
            {
                return true;
            }

            public function delete()
            {
                return $this;
            }

            public function getProductUrl()
            {
                return $this->productUrl;
            }

            public function unsProduct()
            {
                $this->product = null;
                return $this;
            }
        };

        $itemTwoMock = new class extends Item {
            /**
             * @var Product
             */
            public $product;
            /**
             * @var int
             */
            public $id;
            /**
             * @var int
             */
            public $qty;
            /**
             * @var string
             */
            public $productUrl = '';
            /**
             * @var bool
             */
            public $shouldThrowException = false;
            /**
             * @var \Exception
             */
            public $exception;
            /**
             * @var Product
             */
            private $originalProduct;

            public function __construct()
            {
            }

            public function setOriginalProduct($product)
            {
                $this->originalProduct = $product;
                $this->product = $product;
            }

            public function getProduct()
            {
                // Always return the original product for testing purposes
                return $this->originalProduct;
            }

            public function getId()
            {
                return $this->id;
            }

            public function setQty($qty)
            {
                $this->qty = $qty;
                return $this;
            }

            public function addToCart($cart, $delete = false)
            {
                if ($this->shouldThrowException && $this->exception) {
                    throw $this->exception;
                }
                return true;
            }

            public function delete()
            {
                return $this;
            }

            public function getProductUrl()
            {
                return $this->productUrl;
            }

            public function unsProduct()
            {
                $this->product = null;
                return $this;
            }
        };

        $productOneMock = new class extends Product {
            /**
             * @var string
             */
            public $name;
            /**
             * @var bool
             */
            public $disableAddToCart = false;

            public function __construct()
            {
            }

            public function getName()
            {
                return $this->name;
            }

            public function getDisableAddToCart()
            {
                return $this->disableAddToCart;
            }

            public function setDisableAddToCart($value)
            {
                $this->disableAddToCart = $value;
                return $this;
            }
        };

        $productTwoMock = new class extends Product {
            /**
             * @var string
             */
            public $name;
            /**
             * @var bool
             */
            public $disableAddToCart = false;

            public function __construct()
            {
            }

            public function getName()
            {
                return $this->name;
            }

            public function getDisableAddToCart()
            {
                return $this->disableAddToCart;
            }

            public function setDisableAddToCart($value)
            {
                $this->disableAddToCart = $value;
                return $this;
            }
        };

        $itemOneMock->product = $productOneMock;
        $itemOneMock->setOriginalProduct($productOneMock);
        $itemTwoMock->product = $productTwoMock;
        $itemTwoMock->setOriginalProduct($productTwoMock);

        $collection = [$itemOneMock, $itemTwoMock];

        /** @var Wishlist|MockObject $wishlistMock */
        $wishlistMock = $this->getMockBuilder(Wishlist::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->mocks['session']->expects($this->once())
            ->method('getCustomerId')
            ->willReturn($sessionCustomerId);

        $wishlistMock->expects($this->once())
            ->method('isOwner')
            ->with($sessionCustomerId)
            ->willReturn($isOwner);
        $wishlistMock->expects($this->once())
            ->method('getId')
            ->willReturn($wishlistId);

        /** @var Collection|MockObject $collectionMock */
        $collectionMock = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $wishlistMock->expects($this->once())
            ->method('getItemCollection')
            ->willReturn($collectionMock);

        $collectionMock->expects($this->once())
            ->method('setVisibilityFilter')
            ->with(true)
            ->willReturn($collection);
        $itemOneMock->id = $itemOneId;
        $itemTwoMock->id = $itemTwoId;
        $itemOneMock->product = $productOneMock;
        $itemOneMock->setOriginalProduct($productOneMock);
        $itemTwoMock->product = $productTwoMock;
        $itemTwoMock->setOriginalProduct($productTwoMock);

        $this->mocks['quantityProcessor']->expects($this->once())
            ->method('process')
            ->with($qtys[$itemOneId])
            ->willReturnArgument(0);
        $itemOneMock->setQty($qtys[$itemOneId]);

        $exception = new Exception('Exception.');
        $itemTwoMock->shouldThrowException = true;
        $itemTwoMock->exception = $exception;

        $this->mocks['logger']->expects($this->once())
            ->method('critical')
            ->with($exception, []);

        $this->mocks['wishlistHelper']->expects($this->once())
            ->method('getListUrl')
            ->with($wishlistId)
            ->willReturn($indexUrl);

        $this->mocks['cartHelper']->expects($this->once())
            ->method('getShouldRedirectToCart')
            ->with(null)
            ->willReturn(false);

        $this->mocks['redirect']->expects($this->once())
            ->method('getRefererUrl')
            ->willReturn('');

        $wishlistMock->expects($this->once())
            ->method('save')
            ->willThrowException(new Exception());

        $this->mocks['manager']
            ->method('addErrorMessage')
            ->willReturnCallback(function ($arg1, $arg2) {
                if ($arg1 == __('We can\'t add this item to your shopping cart right now.' && $arg2 === null) ||
                    $arg1 == __('We can\'t update the Wish List right now.') && $arg2 === null) {
                    return $this->mocks['manager'];
                }
            });

        $productOneMock->name = $productOneName;
        $productTwoMock->name = $productTwoName;

        $this->mocks['manager']->expects($this->once())
            ->method('addSuccessMessage')
            ->with(__('%1 product(s) have been added to shopping cart: %2.', 1, '"' . $productOneName . '"'), null)
            ->willReturnSelf();

        $this->mocks['cart']->expects($this->once())
            ->method('save')
            ->willReturnSelf();

        /** @var Quote|MockObject $collectionMock */
        $quoteMock = $this->getMockBuilder(Quote::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->mocks['cart']->expects($this->once())
            ->method('getQuote')
            ->willReturn($quoteMock);

        $quoteMock->expects($this->once())
            ->method('collectTotals')
            ->willReturnSelf();

        $this->mocks['wishlistHelper']->expects($this->once())
            ->method('calculate')
            ->willReturnSelf();

        $this->assertEquals($indexUrl, $this->model->moveAllToCart($wishlistMock, $qtys));
    }
}
