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


namespace Magento\Wishlist\Test\Unit\Model\Rss;

use Magento\Catalog\Helper\Image;
use Magento\Catalog\Helper\Output;
use Magento\Catalog\Model\Product;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\CustomerFactory;
use Magento\Directory\Helper\Data;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Pricing\Render;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Rss\Model\RssFactory;
use Magento\Store\Model\ScopeInterface;
use Magento\Wishlist\Block\Customer\Wishlist;
use Magento\Wishlist\Helper\Rss;
use Magento\Wishlist\Model\Item;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class WishlistTest extends TestCase
{
    /**
     * @var \Magento\Wishlist\Model\Rss\Wishlist
     */
    protected $model;

    /**
     * @var \Magento\Wishlist\Block\Customer\Wishlist
     */
    protected $wishlistBlock;

    /**
     * @var RssFactory
     */
    protected $rssFactoryMock;

    /**
     * @var UrlInterface
     */
    protected $urlBuilderMock;

    /**
     * @var Rss
     */
    protected $wishlistHelperMock;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var Image
     */
    protected $imageHelperMock;

    /**
     * @var Output
     */
    protected $catalogOutputMock;

    /**
     * @var Output|MockObject
     */
    protected $layoutMock;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * Set up mock objects for tested class
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->catalogOutputMock = $this->createMock(Output::class);
        $this->rssFactoryMock = $this->createPartialMock(RssFactory::class, ['create']); // @phpstan-ignore-line
        $this->wishlistBlock = $this->createMock(Wishlist::class);
        $this->wishlistHelperMock = $this->createPartialMock(
            Rss::class,
            ['getWishlist', 'getCustomer', 'getCustomerName']
        );
        $this->urlBuilderMock = $this->getMockForAbstractClass(UrlInterface::class);
        $this->scopeConfig = $this->getMockForAbstractClass(ScopeConfigInterface::class);

        $this->imageHelperMock = $this->createMock(Image::class);

        $this->layoutMock = $this->createMock(
            LayoutInterface::class,
            [],
            '',
            true,
            true,
            true,
            ['getBlock']
        );

        $this->customerFactory = $this->createPartialMock(CustomerFactory::class, ['create']);

        $requestMock = $this->getMockForAbstractClass(RequestInterface::class);
        $requestMock->expects($this->any())->method('getParam')->with('sharing_code')
            ->willReturn('somesharingcode');

        $objectManager = new ObjectManager($this);
        $this->model = $objectManager->getObject(
            \Magento\Wishlist\Model\Rss\Wishlist::class,
            [
                'wishlistHelper' => $this->wishlistHelperMock,
                'wishlistBlock' => $this->wishlistBlock,
                'outputHelper' => $this->catalogOutputMock,
                'imageHelper' => $this->imageHelperMock,
                'urlBuilder' => $this->urlBuilderMock,
                'scopeConfig' => $this->scopeConfig,
                'rssFactory' => $this->rssFactoryMock,
                'layout' => $this->layoutMock,
                'request' => $requestMock,
                'customerFactory' => $this->customerFactory
            ]
        );
    }

    public function testGetRssData()
    {
        $wishlistId = 1;
        $customerName = 'Customer Name';
        $title = "$customerName's Wishlist";
        $wishlistModelMock = $this->createWishlistModelMock($wishlistId);
        $customerServiceMock = $this->createMock(CustomerInterface::class);
        $wishlistSharingUrl = 'wishlist/shared/index/1';
        $locale = 'en_US';
        $productUrl = 'http://product.url/';
        $productName = 'Product name';

        $customer = $this->createPartialMock(Customer::class, ['getName', '__wakeup', 'load']);
        $customer->expects($this->once())->method('load')->willReturnSelf();
        $customer->expects($this->once())->method('getName')->willReturn('Customer Name');

        $this->customerFactory->expects($this->once())->method('create')->willReturn($customer);

        $this->wishlistHelperMock->method('getWishlist')->willReturn($wishlistModelMock);
        $this->wishlistHelperMock->method('getCustomer')->willReturn($customerServiceMock);
        $this->urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->willReturn($wishlistSharingUrl);
        $this->scopeConfig->expects($this->any())
            ->method('getValue')
            ->willReturnMap(
                [
                        [
                            'advanced/modules_disable_output/Magento_Rss',
                            ScopeInterface::SCOPE_STORE,
                            null,
                            null,
                        ],
                        [
                            Data::XML_PATH_DEFAULT_LOCALE,
                            ScopeInterface::SCOPE_STORE,
                            null,
                            $locale
                        ],
                    ]
            );

        $staticArgs = [
            'productName' => $productName,
            'productUrl' => $productUrl,
        ];
        
        $wishlistItem = $this->createMock(Item::class);
        $wishlistItemsCollection = [$wishlistItem];
        $productMock = $this->createProductMock($staticArgs['productName']);
        
        $wishlistItem->method('getProduct')->willReturn($productMock);
        
        $wishlistModelMock->setItemCollection($wishlistItemsCollection);
        
        $description = $this->processWishlistItemDescription($wishlistModelMock, $staticArgs);

        $expectedResult = [
            'title' => $title,
            'description' => $title,
            'link' => $wishlistSharingUrl,
            'charset' => 'UTF-8',
            'entries' => [
                0 => [
                    'title' => $productName,
                    'link' => $productUrl,
                    'description' => $description,
                ],
            ],
        ];

        $this->assertEquals($expectedResult, $this->model->getRssData());
    }

    /**
     * Additional function to process forming description for wishlist item
     *
     * @param \Magento\Wishlist\Model\Wishlist $wishlistModelMock
     * @param array $staticArgs
     * @return string
     */
    protected function processWishlistItemDescription($wishlistModelMock, $staticArgs)
    {
        $imgThumbSrc = 'http://source-for-thumbnail';
        $priceHtmlForTest = '<div class="price">Price is 10 for example</div>';

        $wishlistItemsCollection = $wishlistModelMock->getItemCollection();
        $wishlistItem = $wishlistItemsCollection[0];
        $productMock = $wishlistItem->getProduct();
        $this->imageHelperMock->expects($this->once())
            ->method('init')
            ->with($productMock, 'rss_thumbnail')
            ->willReturnSelf();
        $this->imageHelperMock->expects($this->once())
            ->method('getUrl')
            ->willReturn($imgThumbSrc);
        $priceRendererMock = $this->createPartialMock(Render::class, ['render']);

        $this->layoutMock->expects($this->once())
            ->method('getBlock')
            ->willReturn($priceRendererMock);
        $priceRendererMock->expects($this->once())
            ->method('render')
            ->willReturn($priceHtmlForTest);

        $this->catalogOutputMock->expects($this->any())
            ->method('productAttribute')
            ->willReturnArgument(1);
        $this->wishlistBlock
            ->expects($this->any())
            ->method('getProductUrl')
            ->with($productMock, ['_rss' => true])
            ->willReturn($staticArgs['productUrl']);

        $description = '<table><tr><td><a href="' . $staticArgs['productUrl'] . '"><img src="' . $imgThumbSrc .
            '" border="0" align="left" height="75" width="75"></a></td><td style="text-decoration:none;">' .
            'Product short description<p>' . $priceHtmlForTest . '</p><p>Comment: Product description<p>' .
            '</td></tr></table>';

        return $description;
    }

    public function testIsAllowed()
    {
        $customerId = 1;
        $customerServiceMock = $this->createMock(CustomerInterface::class);
        $wishlist = $this->createMock(\Magento\Wishlist\Model\Wishlist::class);
        $wishlist->expects($this->once())->method('getCustomerId')->willReturn($customerId);
        $this->wishlistHelperMock->expects($this->any())->method('getWishlist')
            ->willReturn($wishlist);
        $this->wishlistHelperMock->expects($this->any())
            ->method('getCustomer')
            ->willReturn($customerServiceMock);
        $customerServiceMock->expects($this->once())->method('getId')->willReturn($customerId);
        $this->scopeConfig->expects($this->once())->method('isSetFlag')
            ->with('rss/wishlist/active', ScopeInterface::SCOPE_STORE)
            ->willReturn(true);

        $this->assertTrue($this->model->isAllowed());
    }

    public function testGetCacheKey()
    {
        $wishlistId = 1;
        $wishlist = $this->createMock(\Magento\Wishlist\Model\Wishlist::class);
        $wishlist->expects($this->once())->method('getId')->willReturn($wishlistId);
        $this->wishlistHelperMock->expects($this->any())->method('getWishlist')
            ->willReturn($wishlist);
        $this->assertEquals('rss_wishlist_data_1', $this->model->getCacheKey());
    }

    public function testGetCacheLifetime()
    {
        $this->assertEquals(60, $this->model->getCacheLifetime());
    }

    public function testIsAuthRequired()
    {
        $wishlist = new class() extends \Magento\Wishlist\Model\Wishlist {
            public function __construct()
            {
            }
            
            public function getSharingCode()
            {
                return 'somesharingcode';
            }
        };
        $this->wishlistHelperMock->method('getWishlist')->willReturn($wishlist);
        $this->assertFalse($this->model->isAuthRequired());
    }

    public function testGetProductPriceHtmlBlockDoesntExists()
    {
        $price = 10.;

        $productMock = $this->createMock(Product::class);

        $renderBlockMock = $this->createMock(Render::class);
        $renderBlockMock->expects($this->once())
            ->method('render')
            ->with(
                'wishlist_configured_price',
                $productMock,
                ['zone' => Render::ZONE_ITEM_LIST]
            )
            ->willReturn($price);

        $this->layoutMock->expects($this->once())
            ->method('getBlock')
            ->with('product.price.render.default')
            ->willReturn(false);
        $this->layoutMock->expects($this->once())
            ->method('createBlock')
            ->with(
                Render::class,
                'product.price.render.default',
                ['data' => ['price_render_handle' => 'catalog_product_prices']]
            )
            ->willReturn($renderBlockMock);

        $this->assertEquals($price, $this->model->getProductPriceHtml($productMock));
    }

    public function testGetProductPriceHtmlBlockExists()
    {
        $price = 10.;

        $productMock = $this->createMock(Product::class);

        $renderBlockMock = $this->createMock(Render::class);
        $renderBlockMock->expects($this->once())
            ->method('render')
            ->with(
                'wishlist_configured_price',
                $productMock,
                ['zone' => Render::ZONE_ITEM_LIST]
            )
            ->willReturn($price);

        $this->layoutMock->expects($this->once())
            ->method('getBlock')
            ->with('product.price.render.default')
            ->willReturn($renderBlockMock);

        $this->assertEquals($price, $this->model->getProductPriceHtml($productMock));
    }

    private function createWishlistModelMock($wishlistId)
    {
        return new class($wishlistId) extends \Magento\Wishlist\Model\Wishlist {
            /**
             * @var int
             */
            private $id;
            /**
             * @var string
             */
            private $sharingCode = 'somesharingcode';
            /**
             * @var Collection
             */
            private $itemCollection;
            
            public function __construct($id)
            {
                $this->id = $id;
                $_ = [$id];
                unset($_);
            }
            
            public function getId()
            {
                return $this->id;
            }
            
            public function getSharingCode()
            {
                return $this->sharingCode;
            }
            
            public function getCustomerId()
            {
                return 1;
            }
            
            public function setItemCollection($collection)
            {
                $this->itemCollection = $collection;
                $_ = [$collection];
                unset($_);
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
    }

    private function createProductMock($productName)
    {
        return new class($productName) extends Product {
            /**
             * @var string
             */
            private $name;
            
            public function __construct($name)
            {
                $this->name = $name;
                $_ = [$name];
                unset($_);
            }
            
            public function getName()
            {
                return $this->name;
            }
            public function setAllowedInRss($value)
            {
                return $this;
            }
            public function setAllowedPriceInRss($value)
            {
                return $this;
            }
            public function setProductUrl($url)
            {
                return $this;
            }
            public function getAllowedInRss()
            {
                return true;
            }
            public function getAllowedPriceInRss()
            {
                return true;
            }
            public function getShortDescription()
            {
                return 'Product short description';
            }
            public function getDescription()
            {
                return 'Product description';
            }
        };
    }
}
