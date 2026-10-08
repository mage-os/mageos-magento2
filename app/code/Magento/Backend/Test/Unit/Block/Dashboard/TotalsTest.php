<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Block\Dashboard;

use Magento\Backend\Block\Dashboard\Totals;
use Magento\Backend\Model\Dashboard\Config;
use Magento\Backend\Model\Dashboard\Period;
use Magento\Backend\Model\Dashboard\StatisticsCache;
use Magento\Directory\Model\Currency;
use Magento\Framework\App\ObjectManager as AppObjectManager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Module\Manager;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\View\LayoutInterface;
use Magento\Reports\Model\ResourceModel\Order\Collection;
use Magento\Reports\Model\ResourceModel\Order\CollectionFactory;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TotalsTest extends TestCase
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
     * @var Totals
     */
    private $block;

    protected function setUp(): void
    {
        $appObjectManager = $this->createMock(ObjectManagerInterface::class);
        $appObjectManager->method('get')->willReturnCallback(fn (string $type) => $this->createMock($type));
        AppObjectManager::setInstance($appObjectManager);

        $this->statisticsCache = $this->createMock(StatisticsCache::class);
        $this->config = $this->createMock(Config::class);
        $this->collectionFactory = $this->createMock(CollectionFactory::class);

        $request = $this->createMock(RequestInterface::class);
        $request->method('getParam')->willReturnCallback(
            fn ($name, $default = null) => $name === 'period' ? '7d' : $default
        );

        $moduleManager = $this->createMock(Manager::class);
        $moduleManager->method('isEnabled')->with('Magento_Reports')->willReturn(true);

        $period = $this->createMock(Period::class);
        $period->method('getDatePeriods')->willReturn(['today' => 'Today', '7d' => 'Last 7 Days']);

        $currency = $this->createMock(Currency::class);
        $currency->method('format')->willReturnCallback(fn ($value) => '$' . number_format((float)$value, 2));
        $store = $this->createMock(Store::class);
        $store->method('getBaseCurrency')->willReturn($currency);
        $store->method('getId')->willReturn(0);
        $storeManager = $this->createMock(StoreManagerInterface::class);
        $storeManager->method('getStore')->willReturn($store);

        $this->block = (new ObjectManager($this))->getObject(
            Totals::class,
            [
                'request' => $request,
                'storeManager' => $storeManager,
                'collectionFactory' => $this->collectionFactory,
                'moduleManager' => $moduleManager,
                'period' => $period,
                'statisticsCache' => $this->statisticsCache,
                'dashboardConfig' => $this->config,
            ]
        );
    }

    public function testFiguresComeFromCacheKeyedByPeriodAndScope(): void
    {
        $this->config->method('getTotalsCacheLifetime')->willReturn(300);
        $this->statisticsCache->expects($this->once())
            ->method('get')
            ->with('totals', ['period' => '7d', 'store' => '', 'website' => '', 'group' => ''], 300)
            ->willReturn(['revenue' => 100.0, 'tax' => 8.0, 'shipping' => 5.5, 'quantity' => 3]);
        $this->collectionFactory->expects($this->never())->method('create');

        $this->block->setLayout($this->createMock(LayoutInterface::class));

        $values = array_column($this->block->getTotals(), 'value');
        $this->assertSame(['$100.00', '$8.00', '$5.50', 3], $values);
    }

    public function testLoaderQueriesPeriodFilteredCollection(): void
    {
        $this->config->method('getTotalsCacheLifetime')->willReturn(0);
        $this->statisticsCache->method('get')
            ->willReturnCallback(fn ($key, $scope, $lifetime, $loader) => $loader());

        $collection = $this->createMock(Collection::class);
        $collection->expects($this->once())->method('addCreateAtPeriodFilter')->with('7d')->willReturnSelf();
        $collection->expects($this->once())->method('calculateTotals')->with(false)->willReturnSelf();
        $collection->method('isLive')->willReturn(true);
        $collection->expects($this->never())->method('addFieldToFilter');
        $collection->method('load')->willReturnSelf();
        $collection->method('getFirstItem')->willReturn(
            new DataObject(['revenue' => '42.5', 'tax' => '2', 'shipping' => '0', 'quantity' => '7'])
        );
        $this->collectionFactory->method('create')->willReturn($collection);

        $this->block->setLayout($this->createMock(LayoutInterface::class));

        $values = array_column($this->block->getTotals(), 'value');
        $this->assertSame(['$42.50', '$2.00', '$0.00', 7], $values);
    }
}
