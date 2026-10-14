<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Block\Dashboard;

use Magento\Backend\Block\Dashboard\Sales;
use Magento\Backend\Model\Dashboard\Config;
use Magento\Backend\Model\Dashboard\StatisticsCache;
use Magento\Directory\Model\Currency;
use Magento\Framework\App\ObjectManager as AppObjectManager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Escaper;
use Magento\Framework\Module\Manager;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\UrlInterface;
use Magento\Reports\Model\ResourceModel\Order\Collection;
use Magento\Reports\Model\ResourceModel\Order\CollectionFactory;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SalesTest extends TestCase
{
    /**
     * @var StatisticsCache|MockObject
     */
    private $statisticsCache;

    /**
     * @var Config|MockObject
     */
    private $config;

    /**
     * @var CollectionFactory|MockObject
     */
    private $collectionFactory;

    /**
     * @var RequestInterface|MockObject
     */
    private $request;

    protected function setUp(): void
    {
        $appObjectManager = $this->createMock(ObjectManagerInterface::class);
        $appObjectManager->method('get')->willReturnCallback(
            fn (string $type) => $this->createMock($type)
        );
        AppObjectManager::setInstance($appObjectManager);

        $this->statisticsCache = $this->createMock(StatisticsCache::class);
        $this->config = $this->createMock(Config::class);
        $this->collectionFactory = $this->createMock(CollectionFactory::class);
        $this->request = $this->createMock(RequestInterface::class);
        $this->request->method('getParam')->willReturnCallback(
            fn ($name, $default = null) => $name === 'store' ? '3' : $default
        );
    }

    private function createBlock(array $data = []): Sales
    {
        $moduleManager = $this->createMock(Manager::class);
        $moduleManager->method('isEnabled')->with('Magento_Reports')->willReturn(true);

        $urlBuilder = $this->createMock(UrlInterface::class);
        $urlBuilder->method('getUrl')->willReturnCallback(
            fn ($route, $params) => 'https://admin.test/' . $route . '?block=' . ($params['block'] ?? '')
        );

        $currency = $this->createMock(Currency::class);
        $currency->method('format')->willReturnCallback(fn ($value) => '$' . number_format((float)$value, 2));
        $store = $this->createMock(Store::class);
        $store->method('getBaseCurrency')->willReturn($currency);
        $storeManager = $this->createMock(StoreManagerInterface::class);
        $storeManager->method('getStore')->with('3')->willReturn($store);

        /** @var Sales $block */
        $block = (new ObjectManager($this))->getObject(
            Sales::class,
            [
                'request' => $this->request,
                'urlBuilder' => $urlBuilder,
                'storeManager' => $storeManager,
                'escaper' => new Escaper(),
                'collectionFactory' => $this->collectionFactory,
                'moduleManager' => $moduleManager,
                'data' => $data,
                'statisticsCache' => $this->statisticsCache,
                'dashboardConfig' => $this->config,
            ]
        );

        return $block;
    }

    private function expectCollection(float $lifetime, float $average): void
    {
        $item = new \Magento\Framework\DataObject(['lifetime' => $lifetime, 'average' => $average]);
        $collection = $this->createMock(Collection::class);
        $collection->expects($this->once())->method('calculateSales')->with(true)->willReturnSelf();
        $collection->expects($this->once())->method('addFieldToFilter')->with('store_id', '3')->willReturnSelf();
        $collection->method('load')->willReturnSelf();
        $collection->method('getFirstItem')->willReturn($item);
        $this->collectionFactory->method('create')->willReturn($collection);
    }

    public function testCacheHitRendersFiguresWithoutQuerying(): void
    {
        $this->config->method('getLifetimeCacheLifetime')->willReturn(3600);
        $this->statisticsCache->expects($this->once())
            ->method('load')
            ->with(Sales::CACHE_KEY, ['store' => '3', 'website' => '', 'group' => ''])
            ->willReturn(['lifetime' => 1234.5, 'average' => 61.725]);
        $this->collectionFactory->expects($this->never())->method('create');

        $block = $this->createBlock();
        $block->setLayout($this->createMock(\Magento\Framework\View\LayoutInterface::class));

        $this->assertFalse($block->isDeferred());
        $this->assertSame(
            [
                ['label' => 'Lifetime Sales', 'value' => '$1,234.50', 'decimals' => ''],
                ['label' => 'Average Order', 'value' => '$61.73', 'decimals' => ''],
            ],
            $this->normalize($block->getTotals())
        );
    }

    public function testCacheMissDefersLoading(): void
    {
        $this->config->method('getLifetimeCacheLifetime')->willReturn(3600);
        $this->statisticsCache->method('load')->willReturn(null);
        $this->statisticsCache->expects($this->never())->method('get');
        $this->collectionFactory->expects($this->never())->method('create');

        $block = $this->createBlock();
        $block->setLayout($this->createMock(\Magento\Framework\View\LayoutInterface::class));

        $this->assertTrue($block->isDeferred());
        $totals = $block->getTotals();
        $this->assertCount(2, $totals);
        $this->assertStringContainsString('dashboard-sales-loading', $totals[0]['value']);

        $html = (new \ReflectionMethod($block, '_afterToHtml'))->invoke($block, '<p>inner</p>');
        $this->assertStringStartsWith('<div id="' . Sales::DEFERRED_ELEMENT_ID . '"><p>inner</p>', $html);
        $this->assertStringContainsString('Magento_Backend/js/dashboard/sales', $html);
        $this->assertStringContainsString('adminhtml/dashboard/ajaxBlock?block=sales', $html);
    }

    public function testRenderSyncOnMissQueriesAndCaches(): void
    {
        $this->config->method('getLifetimeCacheLifetime')->willReturn(3600);
        $this->expectCollection(500.0, 50.0);
        $this->statisticsCache->expects($this->never())->method('load');
        $this->statisticsCache->expects($this->once())
            ->method('get')
            ->with(Sales::CACHE_KEY, $this->isArray(), 3600, $this->isCallable())
            ->willReturnCallback(fn ($key, $scope, $lifetime, $loader) => $loader());

        $block = $this->createBlock(['render_sync' => true]);
        $block->setLayout($this->createMock(\Magento\Framework\View\LayoutInterface::class));

        $this->assertFalse($block->isDeferred());
        $this->assertSame('$500.00', $block->getTotals()[0]['value']);
        $this->assertSame('$50.00', $block->getTotals()[1]['value']);

        $html = (new \ReflectionMethod($block, '_afterToHtml'))->invoke($block, '<p>inner</p>');
        $this->assertSame('<p>inner</p>', $html);
    }

    public function testLifetimeZeroQueriesLiveWithoutDeferring(): void
    {
        $this->config->method('getLifetimeCacheLifetime')->willReturn(0);
        $this->expectCollection(10.0, 5.0);
        $this->statisticsCache->expects($this->never())->method('load');
        $this->statisticsCache->expects($this->once())
            ->method('get')
            ->with(Sales::CACHE_KEY, $this->isArray(), 0, $this->isCallable())
            ->willReturnCallback(fn ($key, $scope, $lifetime, $loader) => $loader());

        $block = $this->createBlock();
        $block->setLayout($this->createMock(\Magento\Framework\View\LayoutInterface::class));

        $this->assertFalse($block->isDeferred());
        $this->assertSame('$10.00', $block->getTotals()[0]['value']);
    }

    private function normalize(array $totals): array
    {
        return array_map(
            fn ($t) => ['label' => (string)$t['label'], 'value' => $t['value'], 'decimals' => $t['decimals']],
            $totals
        );
    }
}
