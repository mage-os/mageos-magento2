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

        /** @var Product|MockObject $productOneMock */
        $productOneMock = $this->createPartialMock(Product::class, []);
        $reflection = new \ReflectionClass($productOneMock);
        $dataProperty = $reflection->getProperty('_data');
        $dataProperty->setValue($productOneMock, [
            'name' => $productOneName,
            'disable_add_to_cart' => true
        ]);

        /** @var Product|MockObject $productTwoMock */
        $productTwoMock = $this->createPartialMock(Product::class, []);
        $reflection = new \ReflectionClass($productTwoMock);
        $dataProperty = $reflection->getProperty('_data');
        $dataProperty->setValue($productTwoMock, [
            'name' => $productTwoName,
            'disable_add_to_cart' => false
        ]);

        /** @var Item|MockObject $itemOneMock */
        $itemOneMock = $this->createPartialMock(Item::class, ['addToCart', 'getProduct', 'setQty', 'delete', 'getId']);
        $itemOneMock->method('getId')->willReturn($itemOneId);
        $itemOneMock->method('getProduct')->willReturn($productOneMock);
        $itemOneMock->method('addToCart')->willReturn(false);
        $itemOneMock->method('setQty')->willReturnSelf();
        $itemOneMock->method('delete')->willReturnSelf();
        
        /** @var Item|MockObject $itemTwoMock */
        $itemTwoMock = $this->createPartialMock(Item::class, ['addToCart', 'getProduct', 'setQty', 'delete', 'getId']);
        $itemTwoMock->method('getId')->willReturn($itemTwoId);
        $itemTwoMock->method('getProduct')->willReturn($productTwoMock);
        $itemTwoMock->method('addToCart')->willReturn(true);
        $itemTwoMock->method('setQty')->willReturnSelf();
        $itemTwoMock->method('delete')->willReturnSelf();

        $collection = [$itemOneMock, $itemTwoMock];

        /** @var Wishlist|MockObject $wishlistMock */
        $wishlistMock = $this->createPartialMock(Wishlist::class, ['isOwner', 'getItemCollection', 'save']);
        $reflection = new \ReflectionClass($wishlistMock);
        $property = $reflection->getProperty('_data');
        $property->setValue($wishlistMock, []);
        $wishlistMock->setSharingCode($sharingCode);
        $wishlistMock->setId($wishlistId);
        $wishlistMock->method('isOwner')->with($sessionCustomerId)->willReturn($isOwner);

        $this->mocks['session']->expects($this->once())
            ->method('getCustomerId')
            ->willReturn($sessionCustomerId);

        /** @var Collection|MockObject $collectionMock */
        $collectionMock = $this->createMock(Collection::class);

        $wishlistMock->method('getItemCollection')->willReturn($collectionMock);

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
        $wishlistMock = $this->createPartialMock(Wishlist::class, ['isOwner', 'getItemCollection', 'save']);
        $reflection = new \ReflectionClass($wishlistMock);
        $property = $reflection->getProperty('_data');
        $property->setValue($wishlistMock, []);
        $wishlistMock->setSharingCode($sharingCode);
        $wishlistMock->setId($wishlistId);
        $wishlistMock->method('isOwner')->with($sessionCustomerId)->willReturn($isOwner);

        $this->mocks['session']->expects($this->once())
            ->method('getCustomerId')
            ->willReturn($sessionCustomerId);

        /** @var Collection|MockObject $collectionMock */
        $collectionMock = $this->createMock(Collection::class);

        $wishlistMock->method('getItemCollection')->willReturn($collectionMock);

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
        $quoteMock = $this->createMock(Quote::class);

        $this->mocks['cart']->expects($this->exactly(4))
            ->method('getQuote')
            ->willReturn($quoteMock);

        /** @var Quote\Item|MockObject $collectionMock */
        $itemMock = $this->createMock(Quote\Item::class);

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

        $productOneMock = $this->createPartialMock(Product::class, []);
        $reflection = new \ReflectionClass($productOneMock);
        $dataProperty = $reflection->getProperty('_data');
        $dataProperty->setValue($productOneMock, ['name' => $productOneName]);

        $productTwoMock = $this->createPartialMock(Product::class, []);
        $reflection = new \ReflectionClass($productTwoMock);
        $dataProperty = $reflection->getProperty('_data');
        $dataProperty->setValue($productTwoMock, ['name' => $productTwoName]);

        $itemOneMock = $this->createPartialMock(Item::class, ['addToCart', 'getProduct', 'setQty', 'delete', 'getId']);
        $itemOneMock->method('getId')->willReturn($itemOneId);
        $itemOneMock->method('getProduct')->willReturn($productOneMock);
        $itemOneMock->method('addToCart')->willReturn(true);
        $itemOneMock->method('setQty')->willReturnSelf();
        $itemOneMock->method('delete')->willReturnSelf();

        $exception = new Exception('Exception.');
        $itemTwoMock = $this->createPartialMock(Item::class, ['addToCart', 'getProduct', 'setQty', 'delete', 'getId']);
        $itemTwoMock->method('getId')->willReturn($itemTwoId);
        $itemTwoMock->method('getProduct')->willReturn($productTwoMock);
        $itemTwoMock->method('addToCart')->willThrowException($exception);
        $itemTwoMock->method('setQty')->willReturnSelf();
        $itemTwoMock->method('delete')->willReturnSelf();

        $collection = [$itemOneMock, $itemTwoMock];

        /** @var Wishlist|MockObject $wishlistMock */
        $wishlistMock = $this->createMock(Wishlist::class);

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
        $collectionMock = $this->createMock(Collection::class);

        $wishlistMock->expects($this->once())
            ->method('getItemCollection')
            ->willReturn($collectionMock);

        $collectionMock->expects($this->once())
            ->method('setVisibilityFilter')
            ->with(true)
            ->willReturn($collection);

        $this->mocks['quantityProcessor']->expects($this->once())
            ->method('process')
            ->with($qtys[$itemOneId])
            ->willReturnArgument(0);

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

        $productOneMock->setName($productOneName);
        $productTwoMock->setName($productTwoName);

        $this->mocks['manager']->expects($this->once())
            ->method('addSuccessMessage')
            ->with(__('%1 product(s) have been added to shopping cart: %2.', 1, '"' . $productOneName . '"'), null)
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

        $this->assertEquals($indexUrl, $this->model->moveAllToCart($wishlistMock, $qtys));
    }
}
