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

        $this->storeManager = $this->getMockBuilder(StoreManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->storeManager->expects($this->any())
            ->method('getStore')
            ->willReturn($this->store);

        $this->urlEncoderMock = $this->getMockBuilder(EncoderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->requestMock = $this->createMock(\Magento\Framework\HTTP\PhpEnvironment\Request::class);

        $this->urlBuilder = $this->getMockBuilder(UrlInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

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

        $this->wishlistProvider = $this->getMockBuilder(WishlistProviderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->coreRegistry = $this->createMock(Registry::class);

        $this->postDataHelper = $this->createMock(PostHelper::class);

        $this->wishlistItem = new class extends WishlistItem {
            /** @var int */
            private $wishlistItemId = 1;
            /** @var int|null */
            private $productId = null;
            /** @var int|null */
            private $qty = null;
            /** @var int */
            private $id = 1;
            /** @var mixed */
            private $product = null;

            public function __construct()
            {
                // Don't call parent constructor to avoid dependency issues
            }

            public function getWishlistItemId()
            {
                return $this->wishlistItemId;
            }

            public function getProductId()
            {
                return $this->productId;
            }

            public function getQty()
            {
                return $this->qty;
            }

            public function getId()
            {
                return $this->id;
            }

            public function getProduct()
            {
                return $this->product;
            }

            public function setWishlistItemId($id)
            {
                $this->wishlistItemId = $id;
                return $this;
            }

            public function setProductId($id)
            {
                $this->productId = $id;
                return $this;
            }

            public function setQty($qty) // @SuppressWarnings(PHPMD.UnusedLocalVariable)
            {
                $this->qty = $qty;
                return $this;
            }

            public function setId($id)
            {
                $this->id = $id;
                return $this;
            }

            public function setProduct($product) // @SuppressWarnings(PHPMD.UnusedLocalVariable)
            {
                $this->product = $product;
                return $this;
            }
        };
        $this->wishlistItem->setId(1);
        $this->wishlistItem->setWishlistItemId(1);
        $this->wishlistItem->setProductId(null);
        $this->wishlistItem->setQty(null);
        $this->wishlistItem->setProduct($this->product);

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
        $wishlistItem = new class extends WishlistItem {
            /** @var int */
            private $wishlistItemId = 4;
            /** @var int|null */
            private $productId = null;
            /** @var int */
            private $qty = 0;

            public function __construct()
            {
            }

            public function getWishlistItemId()
            {
                return $this->wishlistItemId;
            }

            public function getProductId()
            {
                return $this->productId;
            }

            public function getQty()
            {
                return $this->qty;
            }
        };

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
        $this->wishlistItem->setWishlistItemId($wishlistItemId);
        $this->wishlistItem->setProduct($this->product);

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
        $this->wishlistItem->setWishlistItemId($wishlistItemId);
        $this->wishlistItem->setProduct($this->product);

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

        $wishlistItem = new class extends WishlistItem {
            /** @var int */
            private $id = 1;
            /** @var int */
            private $wishlistItemId = 1;

            public function __construct()
            {
            }

            public function getId()
            {
                return $this->id;
            }
            public function getWishlistItemId()
            {
                return $this->wishlistItemId;
            }
            public function setId($id)
            {
                $this->id = $id;
                return $this;
            }
            public function setWishlistItemId($id)
            {
                $this->wishlistItemId = $id;
                return $this;
            }
        };
        $wishlistItem->setId($wishlistItemId);
        $wishlistItem->setWishlistItemId($wishlistItemId);

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

        $wishlistItem = new class extends WishlistItem {
            /** @var int */
            private $id = 1;
            /** @var int */
            private $wishlistItemId = 1;

            public function __construct()
            {
            }

            public function getId()
            {
                return $this->id;
            }
            public function getWishlistItemId()
            {
                return $this->wishlistItemId;
            }
            public function setId($id)
            {
                $this->id = $id;
                return $this;
            }
            public function setWishlistItemId($id)
            {
                $this->wishlistItemId = $id;
                return $this;
            }
        };
        $wishlistItem->setId($wishlistItemId);
        $wishlistItem->setWishlistItemId($wishlistItemId);

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
        $this->wishlistItem->setWishlistItemId($wishlistItemId);
        $this->wishlistItem->setProduct($this->product);

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
