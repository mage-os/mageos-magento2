<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Review\Test\Unit\Block\Adminhtml;

use Magento\Catalog\Model\Product as ProductModel;
use Magento\Catalog\Model\Product\Url as ProductUrl;
use Magento\Catalog\Model\ResourceModel\Product;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Magento\Framework\UrlInterface;
use Magento\Review\Block\Adminhtml\Rss;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test RSS adminhtml block
 */
class RssTest extends TestCase
{
    /**
     * @var Rss
     */
    protected $block;

    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * @var StoreManagerInterface|MockObject
     */
    protected $storeManagerInterface;

    /**
     * @var \Magento\Review\Model\Rss|MockObject
     */
    protected $rss;

    /**
     * @var UrlInterface|MockObject
     */
    protected $urlBuilder;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->storeManagerInterface = $this->createPartialMock(\Magento\Store\Model\StoreManager::class, ['getStore']);
        $this->rss = $this->createPartialMock(\Magento\Review\Model\Rss::class, ['__wakeUp', 'getProductCollection']);
        $this->urlBuilder = $this->createMock(UrlInterface::class);
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->block = $this->objectManagerHelper->getObject(
            Rss::class,
            [
                'storeManager' => $this->storeManagerInterface,
                'rssModel' => $this->rss,
                'urlBuilder' => $this->urlBuilder,
            ]
        );
    }

    /**
     * @return void
     */
    public function testGetRssData()
    {
        $rssUrl = '';
        $rssData = [
            'title' => 'Pending product review(s)',
            'description' => 'Pending product review(s)',
            'link' => $rssUrl,
            'charset' => 'UTF-8',
            'entries' => [
                'title' => 'Product: "Product Name" reviewed by: Product Nick',
                'link' => 'http://example.com/product',
                'description' => [
                    'rss_url' => $rssUrl,
                    'name' => 'Product Name',
                    'summary' => 'Product Title',
                    'review' => 'Product Detail',
                    'store' => 'Store Name',

                ],
            ],
        ];
        // Mock URL model to return product URL
        $urlModel = $this->createMock(ProductUrl::class);
        $urlModel->method('getProductUrl')->willReturn('http://example.com/product');
        
        $productModel = $this->createPartialMock(ProductModel::class, ['getUrlModel']);
        
        // Initialize _data array for DataObject magic methods
        $reflection = new \ReflectionClass($productModel);
        $dataProperty = $reflection->getProperty('_data');
        $dataProperty->setValue($productModel, []);
        
        $productModel->method('getUrlModel')->willReturn($urlModel);
        $productModel->setName('Product Name');
        $productModel->setStoreId(1);
        $productModel->setNickname('Product Nick');
        $productModel->setTitle('Product Title');
        $productModel->setDetail('Product Detail');
        $storeModel = $this->createMock(Store::class);
        $this->storeManagerInterface->expects($this->once())->method('getStore')->willReturn($storeModel);
        $storeModel->expects($this->once())->method('getName')
            ->willReturn($rssData['entries']['description']['store']);
        $this->urlBuilder->expects($this->any())->method('getUrl')->willReturn($rssUrl);
        $this->rss->expects($this->once())->method('getProductCollection')
            ->willReturn([$productModel]);

        $data = $this->block->getRssData();

        $this->assertEquals($rssData['title'], $data['title']);
        $this->assertEquals($rssData['description'], $data['description']);
        $this->assertEquals($rssData['link'], $data['link']);
        $this->assertEquals($rssData['charset'], $data['charset']);
        $this->assertEquals($rssData['entries']['title'], $data['entries'][0]['title']);
        $this->assertEquals($rssData['entries']['link'], $data['entries'][0]['link']);
        $this->assertStringContainsString(
            $rssData['entries']['description']['rss_url'],
            $data['entries'][0]['description']
        );
        $this->assertStringContainsString(
            $rssData['entries']['description']['name'],
            $data['entries'][0]['description']
        );
        $this->assertStringContainsString(
            $rssData['entries']['description']['summary'],
            $data['entries'][0]['description']
        );
        $this->assertStringContainsString(
            $rssData['entries']['description']['review'],
            $data['entries'][0]['description']
        );
        $this->assertStringContainsString(
            $rssData['entries']['description']['store'],
            $data['entries'][0]['description']
        );
    }

    /**
     * @return void
     */
    public function testGetCacheLifetime()
    {
        $this->assertEquals(0, $this->block->getCacheLifetime());
    }

    /**
     * @return void
     */
    public function testIsAllowed()
    {
        $this->assertTrue($this->block->isAllowed());
    }

    /**
     * @return void
     */
    public function testGetFeeds()
    {
        $this->assertEquals([], $this->block->getFeeds());
    }
}
