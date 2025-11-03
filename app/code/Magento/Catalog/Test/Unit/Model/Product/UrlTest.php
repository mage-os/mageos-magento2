<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Model\Product;

use PHPUnit\Framework\Attributes\CoversClass;
use Magento\Store\Model\ScopeInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Magento\Catalog\Helper\Category;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Url;
use Magento\Catalog\Model\Product\Url as ProductUrl;
use Magento\Catalog\Test\Unit\Helper\ProductTestHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Filter\Test\Unit\Helper\FilterManagerTestHelper;
use Magento\Framework\Session\SidResolverInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\UrlFactory;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
#[CoversClass(\Magento\Catalog\Model\Product\Url::class)]
class UrlTest extends TestCase
{
    /**
     * @var Url
     */
    protected $model;

    /**
     * @var FilterManager|MockObject
     */
    protected $filter;

    /**
     * @var UrlFinderInterface|MockObject
     */
    protected $urlFinder;

    /**
     * @var Category|MockObject
     */
    protected $catalogCategory;

    /**
     * @var \Magento\Framework\Url|MockObject
     */
    protected $url;

    /**
     * @var SidResolverInterface|MockObject
     */
    protected $sidResolver;

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    protected function setUp(): void
    {
        $this->filter = new FilterManagerTestHelper();

        $this->urlFinder = $this->getMockBuilder(
            UrlFinderInterface::class
        )->disableOriginalConstructor()
            ->getMock();

        $this->url = $this->getMockBuilder(
            \Magento\Framework\Url::class
        )->disableOriginalConstructor()
            ->onlyMethods(
                ['setScope', 'getUrl']
            )->getMock();

        $this->sidResolver = $this->createMock(SidResolverInterface::class);

        $store = $this->createPartialMock(Store::class, ['getId']);
        $store->method('getId')->willReturn(1);
        $storeManager = $this->createMock(StoreManagerInterface::class);
        $storeManager->method('getStore')->willReturn($store);

        $urlFactory = $this->getMockBuilder(UrlFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $urlFactory->method('create')
            ->willReturn($this->url);

        $this->scopeConfig = $this->createMock(ScopeConfigInterface::class);

        $objectManager = new ObjectManager($this);
        $this->model = $objectManager->getObject(
            ProductUrl::class,
            [
                'filter' => $this->filter,
                'catalogCategory' => $this->catalogCategory,
                'storeManager' => $storeManager,
                'urlFactory' => $urlFactory,
                'sidResolver' => $this->sidResolver,
                'scopeConfig' => $this->scopeConfig
            ]
        );
    }

    /**
     * @return void
     */
    public function testFormatUrlKey(): void
    {
        $strIn = 'Some string';
        $resultString = 'some';

        $this->filter->setTranslitUrlResult($resultString);

        $this->scopeConfig->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\Catalog\Helper\Product::XML_PATH_APPLY_TRANSLITERATION_TO_URL,
                ScopeInterface::SCOPE_STORE
            )->willReturn(true);
        $this->assertEquals($resultString, $this->model->formatUrlKey($strIn));
    }

    /**
     * @return void
     */
    public function testFormatUrlKeyWithoutTransliteration(): void
    {
        $strIn = 'Some string ';
        $resultString = 'some-string';

        $this->scopeConfig->expects($this->once())
            ->method('getValue')
            ->with(
                \Magento\Catalog\Helper\Product::XML_PATH_APPLY_TRANSLITERATION_TO_URL,
                ScopeInterface::SCOPE_STORE
            )->willReturn(false);
        $this->assertEquals($resultString, $this->model->formatUrlKey($strIn));
    }

    /**
     *
     * @param $getUrlMethod
     * @param $routePath
     * @param $requestPathProduct
     * @param $storeId
     * @param $categoryId
     * @param $routeParams
     * @param $routeParamsUrl
     * @param $productId
     * @param $productUrlKey
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    #[DataProvider('getUrlDataProvider')]
    public function testGetUrl(
        $getUrlMethod,
        $routePath,
        $requestPathProduct,
        $storeId,
        $categoryId,
        $routeParams,
        $routeParamsUrl,
        $productId,
        $productUrlKey
    ) {
        $product = new ProductTestHelper();
        $product->setStoreId($storeId);
        $product->setCategoryId($categoryId);
        $product->setRequestPath($requestPathProduct);
        $product->setId($productId);
        $product->setUrlKey($productUrlKey);
        $this->url->expects($this->any())->method('setScope')->with($storeId)->willReturnSelf();
        $this->url->expects($this->any())
            ->method('getUrl')
            ->with($routePath, $routeParamsUrl)
            ->willReturn($requestPathProduct);
        $this->urlFinder->method('findOneByData')->willReturn(false);

        switch ($getUrlMethod) {
            case 'getUrl':
                $this->assertEquals($requestPathProduct, $this->model->getUrl($product, $routeParams));
                break;
            case 'getUrlInStore':
                $this->assertEquals($requestPathProduct, $this->model->getUrlInStore($product, $routeParams));
                break;
            case 'getProductUrl':
                $this->assertEquals($requestPathProduct, $this->model->getProductUrl($product, null));
                $this->sidResolver
                    ->expects($this->never())
                    ->method('getUseSessionInUrl')
                    ->willReturn(true);
                break;
        }
    }

    /**
     * @return array
     */
    public static function getUrlDataProvider()
    {
        return [
            [
                'getUrl',
                '',
                '/product/url/path',
                1,
                1,
                ['_scope' => 1],
                ['_scope' => 1, '_direct' => '/product/url/path', '_query' => []],
                null,
                null,
            ], [
                'getUrl',
                'catalog/product/view',
                false,
                1,
                1,
                ['_scope' => 1],
                ['_scope' => 1, '_query' => [], 'id' => 1, 's' => 'urlKey', 'category' => 1],
                1,
                'urlKey',
            ], [
                'getUrlInStore',
                '',
                '/product/url/path',
                1,
                1,
                ['_scope' => 1],
                ['_scope' => 1, '_direct' => '/product/url/path', '_query' => [], '_scope_to_url' => true],
                null,
                null,
            ], [
                'getProductUrl',
                '',
                '/product/url/path',
                1,
                1,
                [],
                ['_direct' => '/product/url/path', '_query' => [], '_nosid' => true],
                null,
                null,
            ]
        ];
    }
}
