<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use PHPUnit\Framework\Attributes\DataProvider;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Helper\Product;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product as ProductModel;
use Magento\Catalog\Model\Session;
use Magento\Framework\DataObject;
use Magento\Framework\Registry;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    /**
     * @var Product
     */
    protected $_productHelper;

    protected function setUp(): void
    {
        $arguments = [
            'reindexPriceIndexerData' => [
                'byDataResult' => ['attribute'],
                'byDataChange' => ['attribute'],
            ],
        ];

        $objectManager = new ObjectManager($this);
        $this->_productHelper = $objectManager->getObject(Product::class, $arguments);
    }

    /**
     * @param mixed $data
     * @param boolean $result
     */
    #[DataProvider('getData')]
    public function testIsDataForPriceIndexerWasChanged($data, $result)
    {
        if (is_callable($data)) {
            $data = $data($this);
        }
        $this->assertEquals($this->_productHelper->isDataForPriceIndexerWasChanged($data), $result);
    }

    protected function getMockForCatalogProduct($method)
    {
        $product = $this->createMock(ProductModel::class);
        if ($method!=null) {
            $product->expects(
                $this->once()
            )->method(
                $method
            )->with(
                'attribute'
            )->willReturn(
                true
            );
        }
        return $product;
    }
    /**
     * Data provider for testIsDataForPriceIndexerWasChanged
     * @return array
     */
    public static function getData()
    {
        $product1 = static fn (self $testCase) => $testCase->getMockForCatalogProduct(null);

        $product2 = static fn (self $testCase) => $testCase->getMockForCatalogProduct("getData");

        $product3 = static fn (self $testCase) => $testCase->getMockForCatalogProduct("dataHasChangedFor");

        return [
            [$product1, false],
            [$product2, true],
            [$product3, true],
            [['attribute' => ''], true],
            [['param' => ''], false],
            ['test', false]
        ];
    }

    /**
     * A null last-visited category id must never reach canBeShowInCategory().
     *
     * The method is declared as taking an int, and around-plugins on it are entitled to rely
     * on that. Passing null through made those plugins use null as an array key, which PHP 8.5
     * deprecates. See magento/magento2#40891.
     */
    public function testInitProductDoesNotResolveCategoryWhenLastVisitedIsNull()
    {
        $product = $this->createMock(ProductModel::class);
        $product->method('getId')->willReturn(1);
        $product->method('isVisibleInCatalog')->willReturn(true);
        $product->method('isVisibleInSiteVisibility')->willReturn(true);
        $product->method('getWebsiteIds')->willReturn([1]);
        $product->expects($this->never())->method('canBeShowInCategory');

        $catalogSession = $this->createSessionMock(null);

        $categoryRepository = $this->createMock(CategoryRepositoryInterface::class);
        $categoryRepository->expects($this->never())->method('get');

        $helper = $this->createHelperForInitProduct($product, $catalogSession, $categoryRepository);

        $this->assertSame($product, $helper->initProduct(1, null, new DataObject()));
    }

    /**
     * A non-null last-visited category id is still passed through unchanged.
     */
    public function testInitProductResolvesCategoryWhenLastVisitedIsPresent()
    {
        $category = $this->createMock(Category::class);

        $product = $this->createMock(ProductModel::class);
        $product->method('getId')->willReturn(1);
        $product->method('isVisibleInCatalog')->willReturn(true);
        $product->method('isVisibleInSiteVisibility')->willReturn(true);
        $product->method('getWebsiteIds')->willReturn([1]);
        $product->expects($this->once())->method('canBeShowInCategory')->with(7)->willReturn(true);

        $catalogSession = $this->createSessionMock(7);

        $categoryRepository = $this->createMock(CategoryRepositoryInterface::class);
        $categoryRepository->expects($this->once())->method('get')->with(7)->willReturn($category);

        $helper = $this->createHelperForInitProduct($product, $catalogSession, $categoryRepository);

        $this->assertSame($product, $helper->initProduct(1, null, new DataObject()));
    }

    /**
     * getLastVisitedCategoryId() is a magic session getter, so it is stubbed through __call().
     */
    private function createSessionMock(?int $lastVisitedCategoryId): Session
    {
        $catalogSession = $this->createMock(Session::class);
        $catalogSession->method('__call')->willReturnCallback(
            static fn (string $method) => $method === 'getLastVisitedCategoryId' ? $lastVisitedCategoryId : null
        );

        return $catalogSession;
    }

    /**
     * Build the helper with the collaborators initProduct() touches before the category lookup.
     */
    private function createHelperForInitProduct(
        ProductModel $product,
        Session $catalogSession,
        CategoryRepositoryInterface $categoryRepository
    ): Product {
        $store = $this->createMock(Store::class);
        $store->method('getId')->willReturn(1);
        $store->method('getWebsiteId')->willReturn(1);

        $storeManager = $this->createMock(StoreManagerInterface::class);
        $storeManager->method('getStore')->willReturn($store);

        $productRepository = $this->createMock(ProductRepositoryInterface::class);
        $productRepository->method('getById')->willReturn($product);

        $objectManager = new ObjectManager($this);

        return $objectManager->getObject(
            Product::class,
            [
                'storeManager' => $storeManager,
                'catalogSession' => $catalogSession,
                'coreRegistry' => $this->createMock(Registry::class),
                'productRepository' => $productRepository,
                'categoryRepository' => $categoryRepository,
            ]
        );
    }
}
