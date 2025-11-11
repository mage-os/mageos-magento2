<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Wishlist\Test\Unit\Helper;

use Magento\Catalog\Model\Product;
use Magento\Customer\Model\Session;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Framework\DataObject;
use Magento\Framework\Registry;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Url\EncoderInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Wishlist\Controller\WishlistProviderInterface;
use Magento\Wishlist\Helper\Data;
use Magento\Wishlist\Model\Item as WishlistItem;
use Magento\Wishlist\Model\Wishlist;
use Magento\Framework\TestFramework\Unit\Helper\MockCreationTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 */
class DataTest extends TestCase
{
    use MockCreationTrait;

    /** @var  Data */
    protected $model;

    /** @var  WishlistProviderInterface|MockObject */
    protected $wishlistProvider;

    /** @var  Registry|MockObject */
    protected $coreRegistry;

    /** @var  PostHelper|MockObject */
    protected $postDataHelper;

    /** @var  WishlistItem|MockObject */
    protected $wishlistItem;

    /** @var  Product|MockObject */
    protected $product;

    /** @var  StoreManagerInterface|MockObject */
    protected $storeManager;

    /** @var  Store|MockObject */
    protected $store;

    /** @var  UrlInterface|MockObject */
    protected $urlBuilder;

    /** @var  Wishlist|MockObject */
    protected $wishlist;

    /** @var  EncoderInterface|MockObject */
    protected $urlEncoderMock;

    /** @var  RequestInterface|MockObject */
    protected $requestMock;

    /** @var  Context|MockObject */
    protected $context;

    /** @var  Session|MockObject */
    protected $customerSession;

    /**
     * Set up mock objects for tested class
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->store = $this->createMock(Store::class);

        $this->storeManager = $this->createMock(StoreManagerInterface::class);
        $this->storeManager->expects($this->any())
            ->method('getStore')
            ->willReturn($this->store);

        $this->urlEncoderMock = $this->createMock(EncoderInterface::class);

        $this->requestMock = $this->createMock(\Magento\Framework\HTTP\PhpEnvironment\Request::class);

        $this->urlBuilder = $this->createMock(UrlInterface::class);

        $this->context = $this->createMock(Context::class);
        $this->context->expects($this->once())
            ->method('getUrlBuilder')
            ->willReturn($this->urlBuilder);
        $this->context->expects($this->once())
            ->method('getUrlEncoder')
            ->willReturn($this->urlEncoderMock);
        $this->context->expects($this->once())
            ->method('getRequest')
            ->willReturn($this->requestMock);

        $this->wishlistProvider = $this->createMock(WishlistProviderInterface::class);

        $this->coreRegistry = $this->createMock(Registry::class);

        $this->postDataHelper = $this->createMock(PostHelper::class);

        $this->wishlistItem = $this->createPartialMockWithReflection(
            WishlistItem::class,
            ['getId', 'getWishlistItemId', 'getProductId', 'getQty', 'getData', 'hasData', 'getProduct',
             'load', 'save', 'getResource']
        );
        $this->wishlistItem->method('getId')->willReturn(1);
        $this->wishlistItem->method('getWishlistItemId')->willReturn(1);
        $this->wishlistItem->method('getProductId')->willReturn(1);
        $this->wishlistItem->method('getQty')->willReturn(0);
        $this->wishlistItem->method('getData')->willReturnCallback(function ($key) {
            if ($key === 'product') {
                return $this->product;
            }
            return null;
        });
        $this->wishlistItem->method('hasData')->willReturnCallback(function ($key) {
            return $key === 'product';
        });
        $this->wishlistItem->method('getProduct')->willReturnCallback(function () {
            return $this->product;
        });
        $this->wishlistItem->method('load')->willReturnSelf();
        $this->wishlistItem->method('save')->willReturnSelf();
        $this->wishlistItem->method('getResource')->willReturn(null);

        $this->wishlist = $this->createMock(Wishlist::class);

        $this->product = $this->createMock(Product::class);

        $this->customerSession = $this->createMock(Session::class);

        $objectManager = new ObjectManager($this);
        $this->model = $objectManager->getObject(
            Data::class,
            [
                'context' => $this->context,
                'customerSession' => $this->customerSession,
                'storeManager' => $this->storeManager,
                'wishlistProvider' => $this->wishlistProvider,
                'coreRegistry' => $this->coreRegistry,
                'postDataHelper' => $this->postDataHelper
            ]
        );
    }

    public function testGetAddToCartUrl()
    {
        $url = 'http://magento.com/wishlist/index/index/wishlist_id/1/?___store=default';

        $this->store->expects($this->once())
            ->method('getUrl')
            ->with('wishlist/index/cart', ['item' => '%item%'])
            ->willReturn($url);

        $this->urlBuilder->expects($this->any())
            ->method('getUrl')
            ->with('wishlist/index/index', ['_current' => true, '_use_rewrite' => true, '_scope_to_url' => true])
            ->willReturn($url);

        $this->assertEquals($url, $this->model->getAddToCartUrl('%item%'));
    }

    public function testGetConfigureUrl()
    {
        $url = 'http://magento2ce/wishlist/index/configure/id/4/product_id/30/qty/1000';

        /** @var WishlistItem $wishlistItem */
        $wishlistItem = $this->createPartialMockWithReflection(
            WishlistItem::class,
            ['getWishlistItemId', 'getProductId', 'getQty', 'load', 'save', 'getResource']
        );
        $wishlistItem->method('getWishlistItemId')->willReturn(4);
        $wishlistItem->method('getProductId')->willReturn(null);
        $wishlistItem->method('getQty')->willReturn(0);
        $wishlistItem->method('load')->willReturnSelf();
        $wishlistItem->method('save')->willReturnSelf();
        $wishlistItem->method('getResource')->willReturn(null);

        $this->urlBuilder->expects($this->once())
            ->method('getUrl')
            ->with('wishlist/index/configure', ['id' => 4, 'product_id' => null, 'qty' => 0])
            ->willReturn($url);

        $this->assertEquals($url, $this->model->getConfigureUrl($wishlistItem));
    }

    public function testGetWishlist()
    {
        $this->wishlistProvider->expects($this->once())
            ->method('getWishlist')
            ->willReturn($this->wishlist);

        $this->assertEquals($this->wishlist, $this->model->getWishlist());
    }

    public function testGetWishlistWithCoreRegistry()
    {
        $this->coreRegistry->expects($this->any())
            ->method('registry')
            ->willReturn($this->wishlist);

        $this->assertEquals($this->wishlist, $this->model->getWishlist());
    }

    public function testGetAddToCartParams()
    {
        $url = 'result url';
        $storeId = 1;
        $wishlistItemId = 1;

        // Configure the test item for this specific test
        $this->wishlistItem->setId($wishlistItemId);
        $this->wishlistItem->setData('product', $this->product);

        $this->product->expects($this->once())
            ->method('isVisibleInSiteVisibility')
            ->willReturn(true);
        $this->product->expects($this->once())
            ->method('getStoreId')
            ->willReturn($storeId);

        $this->urlEncoderMock->expects($this->never())
            ->method('encode');

        $this->store->expects($this->once())
            ->method('getUrl')
            ->with('wishlist/index/cart')
            ->willReturn($url);

        $expected = [
            'item' => $wishlistItemId,
            'qty' => null,
            ActionInterface::PARAM_NAME_URL_ENCODED => '',
        ];
        $this->postDataHelper->expects($this->once())
            ->method('getPostData')
            ->with($url, $expected)
            ->willReturn($url);

        $this->assertEquals($url, $this->model->getAddToCartParams($this->wishlistItem));
    }

    public function testGetAddToCartParamsWithReferer()
    {
        $url = 'result url';
        $storeId = 1;
        $wishlistItemId = 1;
        $referer = 'referer';
        $refererEncoded = 'referer_encoded';

        // Configure the test item for this specific test
        $this->wishlistItem->setId($wishlistItemId);
        $this->wishlistItem->setData('product', $this->product);

        $this->product->expects($this->once())
            ->method('isVisibleInSiteVisibility')
            ->willReturn(true);
        $this->product->expects($this->once())
            ->method('getStoreId')
            ->willReturn($storeId);

        $this->requestMock->expects($this->once())
            ->method('getServer')
            ->with('HTTP_REFERER')
            ->willReturn($referer);

        $this->urlEncoderMock->expects($this->once())
            ->method('encode')
            ->with($referer)
            ->willReturn($refererEncoded);

        $this->store->expects($this->once())
            ->method('getUrl')
            ->with('wishlist/index/cart')
            ->willReturn($url);

        $expected = [
            'item' => $wishlistItemId,
            ActionInterface::PARAM_NAME_URL_ENCODED => $refererEncoded,
            'qty' => null,
        ];
        $this->postDataHelper->expects($this->once())
            ->method('getPostData')
            ->with($url, $expected)
            ->willReturn($url);

        $this->assertEquals($url, $this->model->getAddToCartParams($this->wishlistItem, true));
    }

    public function testGetRemoveParams()
    {
        $url = 'result url';
        $wishlistItemId = 1;

        $wishlistItem = $this->createPartialMockWithReflection(
            WishlistItem::class,
            ['getId', 'getWishlistItemId', 'load', 'save', 'getResource']
        );
        $wishlistItem->method('getId')->willReturn($wishlistItemId);
        $wishlistItem->method('getWishlistItemId')->willReturn($wishlistItemId);
        $wishlistItem->method('load')->willReturnSelf();
        $wishlistItem->method('save')->willReturnSelf();
        $wishlistItem->method('getResource')->willReturn(null);

        $this->urlEncoderMock->expects($this->never())
            ->method('encode');

        $this->urlBuilder->expects($this->once())
            ->method('getUrl')
            ->with('wishlist/index/remove', [])
            ->willReturn($url);

        $this->postDataHelper->expects($this->once())
            ->method('getPostData')
            ->with($url, ['item' => $wishlistItemId, ActionInterface::PARAM_NAME_URL_ENCODED => ''])
            ->willReturn($url);

        $this->assertEquals($url, $this->model->getRemoveParams($wishlistItem));
    }

    public function testGetRemoveParamsWithReferer()
    {
        $url = 'result url';
        $wishlistItemId = 1;
        $referer = 'referer';
        $refererEncoded = 'referer_encoded';

        $wishlistItem = $this->createPartialMockWithReflection(
            WishlistItem::class,
            ['getId', 'getWishlistItemId', 'load', 'save', 'getResource']
        );
        $wishlistItem->method('getId')->willReturn($wishlistItemId);
        $wishlistItem->method('getWishlistItemId')->willReturn($wishlistItemId);
        $wishlistItem->method('load')->willReturnSelf();
        $wishlistItem->method('save')->willReturnSelf();
        $wishlistItem->method('getResource')->willReturn(null);

        $this->requestMock->expects($this->once())
            ->method('getServer')
            ->with('HTTP_REFERER')
            ->willReturn($referer);

        $this->urlEncoderMock->expects($this->once())
            ->method('encode')
            ->with($referer)
            ->willReturn($refererEncoded);

        $this->urlBuilder->expects($this->once())
            ->method('getUrl')
            ->with('wishlist/index/remove', [])
            ->willReturn($url);

        $this->postDataHelper->expects($this->once())
            ->method('getPostData')
            ->with($url, ['item' => $wishlistItemId, ActionInterface::PARAM_NAME_URL_ENCODED => $refererEncoded])
            ->willReturn($url);

        $this->assertEquals($url, $this->model->getRemoveParams($wishlistItem, true));
    }

    public function testGetSharedAddToCartUrl()
    {
        $url = 'result url';
        $storeId = 1;
        $wishlistItemId = 1;

        // Configure the test item for this specific test
        $this->wishlistItem->setId($wishlistItemId);
        $this->wishlistItem->setData('product', $this->product);

        $this->product->expects($this->once())
            ->method('isVisibleInSiteVisibility')
            ->willReturn(true);
        $this->product->expects($this->once())
            ->method('getStoreId')
            ->willReturn($storeId);

        $this->store->expects($this->once())
            ->method('getUrl')
            ->with('wishlist/shared/cart')
            ->willReturn($url);

        $expected = [
            'item' => $wishlistItemId,
            'qty' => null,
        ];
        $this->postDataHelper->expects($this->once())
            ->method('getPostData')
            ->with($url, $expected)
            ->willReturn($url);

        $this->assertEquals($url, $this->model->getSharedAddToCartUrl($this->wishlistItem));
    }

    public function testGetSharedAddAllToCartUrl()
    {
        $url = 'result url';

        $this->store->expects($this->once())
            ->method('getUrl')
            ->with('*/*/allcart', ['_current' => true])
            ->willReturn($url);

        $this->postDataHelper->expects($this->once())
            ->method('getPostData')
            ->with($url)
            ->willReturn($url);

        $this->assertEquals($url, $this->model->getSharedAddAllToCartUrl());
    }

    public function testGetRssUrlWithCustomerNotLogin()
    {
        $url = 'result url';

        $this->customerSession->expects($this->once())
            ->method('isLoggedIn')
            ->willReturn(false);

        $this->urlBuilder->expects($this->once())
            ->method('getUrl')
            ->with('wishlist/index/rss', [])
            ->willReturn($url);

        $this->assertEquals($url, $this->model->getRssUrl());
    }
}
