<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Block\Product\Widget;

use Magento\Catalog\Block\Product\Context as ProductBlockContext;
use Magento\Catalog\Block\Product\Widget\Html\Pager;
use Magento\Catalog\Block\Product\Widget\NewWidget;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Directory\Model\Currency;
use Magento\Framework\App\Cache\State;
use Magento\Framework\App\Config;
use Magento\Framework\App\Http\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Manager;
use Magento\Framework\Pricing\Render;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Stdlib\DateTime\Timezone;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Magento\Framework\View\Design\ThemeInterface;
use Magento\Framework\View\DesignInterface;
use Magento\Framework\View\Layout;
use Magento\Framework\View\LayoutInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class NewWidgetTest extends TestCase
{
    /**
     * @var NewWidget
     */
    private NewWidget $block;

    /**
     * @var LayoutInterface|MockObject
     */
    protected $layout;

    /**
     * @var RequestInterface|MockObject
     */
    protected $requestMock;

    /** @var \Magento\Backend\Block\Context|MockObject */
    protected $context;

    /** @var ObjectManagerHelper */
    protected $objectManager;

    /** @var Manager|MockObject */
    protected $eventManager;

    /** @var Config|MockObject */
    protected $scopeConfig;

    /** @var State|MockObject */
    protected $cacheState;

    /** @var \Magento\Catalog\Model\Config|MockObject */
    protected $catalogConfig;

    /** @var Timezone|MockObject */
    protected $localDate;

    /** @var Collection|MockObject */
    protected $productCollection;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManagerHelper($this);
        $this->eventManager = $this->createPartialMock(Manager::class, ['dispatch']);
        $this->scopeConfig = $this->createMock(Config::class);
        $this->cacheState = $this->createPartialMock(State::class, ['isEnabled']);
        $this->localDate = $this->createMock(Timezone::class);
        $this->catalogConfig = $this->getMockBuilder(\Magento\Catalog\Model\Config::class)
            ->onlyMethods(['getProductAttributes'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->layout = $this->createMock(Layout::class);
        $this->requestMock = $this->getMockBuilder(RequestInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->context = $this->getMockBuilder(ProductBlockContext::class)
            ->onlyMethods(
                [
                    'getEventManager', 'getScopeConfig', 'getLayout',
                    'getRequest', 'getCacheState', 'getCatalogConfig',
                    'getLocaleDate'
                ]
            )
            ->disableOriginalConstructor()
            ->disableArgumentCloning()
            ->getMock();

        $this->context->expects($this->any())
            ->method('getLayout')
            ->willReturn($this->layout);
        $this->context->expects($this->any())
            ->method('getRequest')
            ->willReturn($this->requestMock);

        $this->block = $this->objectManager->getObject(
            NewWidget::class,
            [
                'context' => $this->context
            ]
        );
    }

    /**
     * @dataProvider getProductPriceHtmlDataProvider
     */
    public function testGetProductPriceHtml($args)
    {
        $id = 6;
        $expectedHtml = '
        <div class="price-box price-final_price">
            <span class="regular-price" id="product-price-' . $id . '">
                <span class="price">$0.00</span>
            </span>
        </div>';
        $type = 'widget-new-list';
        $productMock = $this->createPartialMock(Product::class, ['getId']);
        $productMock->expects($this->any())
            ->method('getId')
            ->willReturn($id);
        $arguments = [
            'price_id' => 'old-price-' . $id . '-' . $type,
            'display_minimal_price' => true,
            'include_container' => true,
            'zone' => Render::ZONE_ITEM_LIST,
        ];

        $priceBoxMock = $this->createPartialMock(Render::class, ['render']);

        $this->layout->expects($this->once())
            ->method('getBlock')
            ->with('product.price.render.default')
            ->willReturn($priceBoxMock);

        $priceBoxMock->expects($this->once())
            ->method('render')
            ->with('final_price', $productMock, $arguments)
            ->willReturn($expectedHtml);

        $result = $this->block->getProductPriceHtml($productMock, $type, Render::ZONE_ITEM_LIST, $args);
        $this->assertSame($expectedHtml, $result);
    }

    /**
     * Data provider for testGetProductPriceHtml()
     *
     * @return array
     */
    public static function getProductPriceHtmlDataProvider()
    {
        return [
            'without-arguments' => [
                []
            ],
            'with-arguments' => [
                [
                    'zone' => Render::ZONE_ITEM_LIST,
                    'price_id' => 'old-price-6-widget-new-list',
                    'include_container' => true,
                    'display_minimal_price' => true
                ]
            ]
        ];
    }

    /**
     * @param int $pageNumber
     * @param int $expectedResult
     * @dataProvider getCurrentPageDataProvider
     */
    public function testGetCurrentPage($pageNumber, $expectedResult)
    {
        $this->block->setData('page_var_name', 'page_number');

        $this->requestMock->expects($this->any())
            ->method('getParam')
            ->with('page_number')
            ->willReturn($pageNumber);

        $this->assertSame($expectedResult, $this->block->getCurrentPage());
    }

    /**
     * @return array
     */
    public static function getCurrentPageDataProvider(): array
    {
        return [
            'page_one' => [
                'pageNumber' => 1,
                'expectedResult' => 1,
            ],
            'page_five' => [
                'pageNumber' => 5,
                'expectedResult' => 5,
            ],
            'page_ten' => [
                'pageNumber' => 10,
                'expectedResult' => 10,
            ],
        ];
    }

    /**
     * Unit test for getDisplayType()
     */
    public function testGetDisplayType()
    {
        $this->assertSame(NewWidget::DISPLAY_TYPE_ALL_PRODUCTS, $this->block->getDisplayType());
        $this->block->setData('display_type', NewWidget::DISPLAY_TYPE_NEW_PRODUCTS);
        $this->assertSame(NewWidget::DISPLAY_TYPE_NEW_PRODUCTS, $this->block->getDisplayType());
    }

    /**
     * Unit test for showPager()
     */
    public function testShowPager()
    {
        $this->assertFalse($this->block->showPager());
        $this->block->setData('show_pager', 10);
        $this->assertTrue($this->block->showPager());
    }

    public function testGetProductsCount()
    {
        $this->assertSame(10, $this->block->getProductsCount());
        $this->block->setProductsCount(2);
        $this->assertSame(2, $this->block->getProductsCount());
    }

    /**
     * @return void
     */
    protected function generalGetProductCollection()
    {
        $this->eventManager->expects($this->exactly(2))->method('dispatch')
            ->willReturn(true);
        $this->scopeConfig->expects($this->once())->method('getValue')->withAnyParameters()
            ->willReturn(false);
        $this->cacheState->expects($this->atLeastOnce())->method('isEnabled')->withAnyParameters()
            ->willReturn(false);
        $this->catalogConfig->expects($this->once())->method('getProductAttributes')
            ->willReturn([]);
        $this->localDate->expects($this->any())->method('date')
            ->willReturn(new \DateTime('now', new \DateTimeZone('UTC')));

        $this->context->expects($this->once())->method('getEventManager')->willReturn($this->eventManager);
        $this->context->expects($this->once())->method('getScopeConfig')->willReturn($this->scopeConfig);
        $this->context->expects($this->once())->method('getCacheState')->willReturn($this->cacheState);
        $this->context->expects($this->once())->method('getCatalogConfig')->willReturn($this->catalogConfig);
        $this->context->expects($this->once())->method('getLocaleDate')->willReturn($this->localDate);

        $this->productCollection = $this->getMockBuilder(Collection::class)
            ->onlyMethods(
                [
                    'setVisibility', 'addMinimalPrice', 'addFinalPrice',
                    'addTaxPercents', 'addAttributeToSelect', 'addUrlRewrite',
                    'addStoreFilter', 'addAttributeToSort', 'setPageSize',
                    'setCurPage', 'addAttributeToFilter'
                ]
            )
            ->disableOriginalConstructor()
            ->getMock();
        $this->productCollection->expects($this->once())->method('setVisibility')
            ->willReturnSelf();
        $this->productCollection->expects($this->once())->method('addMinimalPrice')
            ->willReturnSelf();
        $this->productCollection->expects($this->once())->method('addFinalPrice')
            ->willReturnSelf();
        $this->productCollection->expects($this->once())->method('addTaxPercents')
            ->willReturnSelf();
        $this->productCollection->expects($this->once())->method('addAttributeToSelect')
            ->willReturnSelf();
        $this->productCollection->expects($this->once())->method('addUrlRewrite')
            ->willReturnSelf();
        $this->productCollection->expects($this->once())->method('addStoreFilter')
            ->willReturnSelf();
        $this->productCollection->expects($this->once())->method('addAttributeToSort')
            ->willReturnSelf();
        $this->productCollection->expects($this->atLeastOnce())->method('setCurPage')
            ->willReturnSelf();
        $this->productCollection->expects($this->any())->method('addAttributeToFilter')
            ->willReturnSelf();
    }

    /**
     * @param string $displayType
     * @param bool $pagerEnable
     * @param int $productsCount
     * @param int $productsPerPage
     */
    protected function startTestGetProductCollection($displayType, $pagerEnable, $productsCount, $productsPerPage)
    {
        $productCollectionFactory = $this->createPartialMock(
            CollectionFactory::class,
            ['create']
        );
        $productCollectionFactory->expects($this->atLeastOnce())->method('create')
            ->willReturn($this->productCollection);

        $this->block = $this->objectManager->getObject(
            NewWidget::class,
            [
                'context' => $this->context,
                'productCollectionFactory' => $productCollectionFactory
            ]
        );

        if (null === $productsPerPage) {
            $this->block->unsetData('products_per_page');
        } else {
            $this->block->setData('products_per_page', $productsPerPage);
        }

        $this->block->setData('show_pager', $pagerEnable);
        $this->block->setData('display_type', $displayType);
        $this->block->setProductsCount($productsCount);
        $this->block->toHtml();
    }

    /**
     * Test protected `_getProductCollection` and `getPageSize` methods via public `toHtml` method,
     * for display_type == DISPLAY_TYPE_NEW_PRODUCTS.
     *
     * @param bool $pagerEnable
     * @param int $productsCount
     * @param int $productsPerPage
     * @param int $expectedPageSize
     * @dataProvider getProductCollectionDataProvider
     */
    public function testGetProductNewCollection($pagerEnable, $productsCount, $productsPerPage, $expectedPageSize)
    {
        $this->generalGetProductCollection();

        $this->productCollection->expects($this->exactly(2))->method('setPageSize')
            ->willReturnCallback(
                function ($arg1) use ($productsCount, $expectedPageSize) {
                    if ($arg1 == $productsCount || $arg1 == $expectedPageSize) {
                        return $this->productCollection;
                    }
                }
            );

        $this->startTestGetProductCollection(
            NewWidget::DISPLAY_TYPE_NEW_PRODUCTS,
            $pagerEnable,
            $productsCount,
            $productsPerPage
        );
    }

    /**
     * Test protected `_getProductCollection` and `getPageSize` methods via public `toHtml` method,
     * for display_type == DISPLAY_TYPE_ALL_PRODUCTS.
     *
     * @param bool $pagerEnable
     * @param int $productsCount
     * @param int $productsPerPage
     * @param int $expectedPageSize
     * @dataProvider getProductCollectionDataProvider
     */
    public function testGetProductAllCollection($pagerEnable, $productsCount, $productsPerPage, $expectedPageSize)
    {
        $this->generalGetProductCollection();

        $this->productCollection->expects($this->atLeastOnce())->method('setPageSize')->with($expectedPageSize)
            ->willReturnSelf();

        $this->startTestGetProductCollection(
            NewWidget::DISPLAY_TYPE_ALL_PRODUCTS,
            $pagerEnable,
            $productsCount,
            $productsPerPage
        );
    }

    /**
     * @return array
     */
    public static function getProductCollectionDataProvider(): array
    {
        return [
            'pager_enabled_count_1_no_custom_per_page' => [
                'pagerEnable'      => true,
                'productsCount'    => 1,
                'productsPerPage'  => null,
                'expectedPageSize' => 5,
            ],
            'pager_enabled_count_5_no_custom_per_page' => [
                'pagerEnable'      => true,
                'productsCount'    => 5,
                'productsPerPage'  => null,
                'expectedPageSize' => 5,
            ],
            'pager_enabled_count_10_no_custom_per_page' => [
                'pagerEnable'      => true,
                'productsCount'    => 10,
                'productsPerPage'  => null,
                'expectedPageSize' => 5,
            ],

            'pager_enabled_custom_per_page_2' => [
                'pagerEnable'      => true,
                'productsCount'    => 1,
                'productsPerPage'  => 2,
                'expectedPageSize' => 2,
            ],
            'pager_enabled_custom_per_page_3' => [
                'pagerEnable'      => true,
                'productsCount'    => 5,
                'productsPerPage'  => 3,
                'expectedPageSize' => 3,
            ],
            'pager_enabled_custom_per_page_7' => [
                'pagerEnable'      => true,
                'productsCount'    => 10,
                'productsPerPage'  => 7,
                'expectedPageSize' => 7,
            ],

            'pager_disabled_count_1_no_custom_per_page' => [
                'pagerEnable'      => false,
                'productsCount'    => 1,
                'productsPerPage'  => null,
                'expectedPageSize' => 1,
            ],
            'pager_disabled_count_3_no_custom_per_page' => [
                'pagerEnable'      => false,
                'productsCount'    => 3,
                'productsPerPage'  => null,
                'expectedPageSize' => 3,
            ],
            'pager_disabled_count_5_no_custom_per_page' => [
                'pagerEnable'      => false,
                'productsCount'    => 5,
                'productsPerPage'  => null,
                'expectedPageSize' => 5,
            ],

            'pager_disabled_with_custom_per_page_3' => [
                'pagerEnable'      => false,
                'productsCount'    => 1,
                'productsPerPage'  => 3,
                'expectedPageSize' => 1,
            ],
            'pager_disabled_with_custom_per_page_5' => [
                'pagerEnable'      => false,
                'productsCount'    => 3,
                'productsPerPage'  => 5,
                'expectedPageSize' => 3,
            ],
            'pager_disabled_with_custom_per_page_10' => [
                'pagerEnable'      => false,
                'productsCount'    => 5,
                'productsPerPage'  => 10,
                'expectedPageSize' => 5,
            ],
        ];
    }

    /**
     * Unit test for getPagerHtml()
     */
    public function testGetPagerHtml()
    {
        $pagerMock = $this->getMockBuilder(Pager::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['setShowPerPage', 'setPageVarName', 'setLimit', 'setCollection', 'toHtml'])
            ->addMethods(['setUseContainer', 'setShowAmounts', 'setTotalLimit'])
            ->getMock();

        $pagerMock->method('setUseContainer')->willReturnSelf();
        $pagerMock->method('setShowAmounts')->willReturnSelf();
        $pagerMock->method('setShowPerPage')->willReturnSelf();
        $pagerMock->method('setPageVarName')->willReturnSelf();
        $pagerMock->method('setLimit')->willReturnSelf();
        $pagerMock->method('setTotalLimit')->willReturnSelf();
        $pagerMock->method('setCollection')->willReturnSelf();
        $pagerMock->expects($this->once())->method('toHtml')->willReturn('pager-html');
        $this->layout->expects($this->once())
            ->method('createBlock')
            ->with(Pager::class, 'widget.new.product.list.pager')
            ->willReturn($pagerMock);

        $this->block->setData('show_pager', true);
        $this->assertSame('pager-html', $this->block->getPagerHtml());
    }

    /**
     * Unit test for getPagerHtml() when show_pager is false
     */
    public function testGetPagerHtmlWhenShowPagerFalse()
    {
        $this->block->setData('show_pager', false);
        $this->assertEmpty($this->block->getPagerHtml());
    }

    /**
     * Unit test for getCacheKeyInfo()
     */
    public function testGetCacheKeyInfo()
    {
        $serializer = $this->createMock(Json::class);
        $httpContext = $this->createMock(Context::class);
        $currency = $this->createMock(Currency::class);
        $store = $this->createMock(Store::class);
        $storeManager = $this->createMock(StoreManagerInterface::class);
        $theme = $this->createMock(ThemeInterface::class);
        $theme->method('getId')->willReturn('theme-id');
        $design = $this->createMock(DesignInterface::class);

        $serializer->expects($this->once())->method('serialize')
            ->with(['foo' => 'bar'])
            ->willReturn('serialized-params');
        $httpContext->method('getValue')->willReturnMap([
            [\Magento\Customer\Model\Context::CONTEXT_GROUP, null],
            [Context::CONTEXT_CURRENCY, null]
        ]);
        $currency->method('getCode')->willReturn('USD');
        $store->method('getDefaultCurrency')->willReturn($currency);
        $store->method('getId')->willReturn(1);
        $storeManager->method('getStore')->willReturn($store);
        $design->method('getDesignTheme')->willReturn($theme);

        $this->requestMock->expects($this->once())->method('getParam')->with('page_number', 1)->willReturn(2);
        $this->requestMock->expects($this->once())->method('getParams')->willReturn(['foo' => 'bar']);

        $block = $this->objectManager->getObject(
            NewWidget::class,
            [
                'context' => $this->context,
                'httpContext' => $httpContext,
                'serializer' => $serializer
            ]
        );

        $ref = new \ReflectionClass($block);
        $prop = $ref->getProperty('_storeManager');
        $prop->setAccessible(true);
        $prop->setValue($block, $storeManager);
        $prop = $ref->getProperty('_design');
        $prop->setAccessible(true);
        $prop->setValue($block, $design);

        $block->setData('page_var_name', 'page_number');

        $result = $block->getCacheKeyInfo();

        $this->assertIsArray($result);
        $this->assertTrue(in_array(NewWidget::DISPLAY_TYPE_ALL_PRODUCTS, $result, true));
        $this->assertTrue(in_array(5, $result, true));
        $this->assertTrue(in_array(2, $result, true));
        $this->assertTrue(in_array('serialized-params', $result, true));
        $this->assertTrue(in_array('USD', $result, true));
    }

    /**
     * Unit test for getProductsPerPage() using data provider
     *
     * @dataProvider getProductsPerPageDataProvider
     * @param int|null $productsPerPage
     * @param int $expectedResult
     * @return void
     */
    public function testGetProductsPerPageWithDataProvider(?int $productsPerPage, int $expectedResult): void
    {
        if ($productsPerPage === null) {
            $this->block->unsetData('products_per_page');
        } else {
            $this->block->setData('products_per_page', $productsPerPage);
        }

        $this->assertSame($expectedResult, $this->block->getProductsPerPage());
    }

    /**
     * Data provider for testGetProductsPerPageWithDataProvider()
     *
     * @return array
     */
    public static function getProductsPerPageDataProvider(): array
    {
        return [
            'default_when_not_set' => [
                'productsPerPage' => null,
                'expectedResult' => NewWidget::DEFAULT_PRODUCTS_PER_PAGE
            ],
            'custom_positive_value' => [
                'productsPerPage' => 7,
                'expectedResult' => 7
            ],
            'explicit_zero_value' => [
                'productsPerPage' => 0,
                'expectedResult' => 0
            ]
        ];
    }
}
