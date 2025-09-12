<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Review\Test\Unit\Block\Adminhtml;

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
                'link' => 'http://product.magento.com',
                'description' => [
                    'rss_url' => $rssUrl,
                    'name' => 'Product Name',
                    'summary' => 'Product Title',
                    'review' => 'Product Detail',
                    'store' => 'Store Name',

                ],
            ],
        ];
        $productModel = new class extends Product {
            public function __construct()
            {
 /* Skip parent constructor */
            }
            public function getStoreId()
            {
                return 1;
            }
            public function getId()
            {
                return 1;
            }
            public function getReviewId()
            {
                return 1;
            }
            public function getNickName()
            {
                return 'Product Nick';
            }
            public function getName()
            {
                return 'Product Name';
            }
            public function getDetail()
            {
                return 'Product Detail';
            }
            public function getTitle()
            {
                return 'Product Title';
            }
            public function getProductUrl()
            {
                return 'http://product.magento.com';
            }
        };
        $storeModel = $this->createMock(Store::class);
        $this->storeManagerInterface->expects($this->once())->method('getStore')->willReturn($storeModel);
        $storeModel->expects($this->once())->method('getName')
            ->willReturn($rssData['entries']['description']['store']);
        $this->urlBuilder->expects($this->any())->method('getUrl')->willReturn($rssUrl);
        $this->urlBuilder->expects($this->once())->method('setScope')->willReturnSelf();
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
