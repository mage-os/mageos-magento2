<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\ViewModel;

use Magento\Backend\ViewModel\StatisticsFreshness;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Module\Manager;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Reports\Model\Flag;
use Magento\Reports\Model\FlagFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class StatisticsFreshnessTest extends TestCase
{
    /**
     * @var Flag|MockObject
     */
    private $flag;

    /**
     * @var ScopeConfigInterface|MockObject
     */
    private $scopeConfig;

    /**
     * @var Manager|MockObject
     */
    private $moduleManager;

    /**
     * @var StatisticsFreshness
     */
    private $viewModel;

    protected function setUp(): void
    {
        $this->flag = $this->createPartialMock(Flag::class, ['setReportFlagCode', 'loadSelf']);
        $this->flag->method('setReportFlagCode')->with(Flag::REPORT_ORDER_FLAG_CODE)->willReturnSelf();
        $this->flag->method('loadSelf')->willReturnSelf();

        $flagFactory = $this->createMock(FlagFactory::class);
        $flagFactory->method('create')->willReturn($this->flag);

        $this->moduleManager = $this->createMock(Manager::class);
        $this->moduleManager->method('isEnabled')->willReturn(true);

        $this->scopeConfig = $this->createMock(ScopeConfigInterface::class);

        $localeDate = $this->createMock(TimezoneInterface::class);
        $localeDate->method('formatDateTime')->willReturnCallback(fn ($date) => $date->format('Y-m-d H:i'));

        $this->viewModel = new StatisticsFreshness($flagFactory, $this->moduleManager, $this->scopeConfig, $localeDate);
    }

    public function testNeverRefreshed(): void
    {
        $this->assertFalse($this->viewModel->isRefreshed());
        $this->assertNull($this->viewModel->getLastRefreshedAt());
        $this->assertNull($this->viewModel->getLastRefreshedAtFormatted());
        $this->assertTrue($this->viewModel->isStale());
    }

    public function testRecentRefreshIsFresh(): void
    {
        $recent = (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))->modify('-2 hours');
        $this->flag->setData('last_update', $recent->format('Y-m-d H:i:s'));

        $this->assertTrue($this->viewModel->isRefreshed());
        $this->assertSame($recent->format('Y-m-d H:i'), $this->viewModel->getLastRefreshedAtFormatted());
        $this->assertFalse($this->viewModel->isStale());
        $this->assertTrue($this->viewModel->isStale(1));
    }

    public function testOldRefreshIsStale(): void
    {
        $this->flag->setData('last_update', '2020-01-01 00:00:00');

        $this->assertTrue($this->viewModel->isStale());
    }

    public function testAggregatedModeReadsSalesConfig(): void
    {
        $this->scopeConfig->expects($this->once())
            ->method('isSetFlag')
            ->with('sales/dashboard/use_aggregated_data')
            ->willReturn(true);

        $this->assertTrue($this->viewModel->isAggregatedMode());
    }

    public function testReportsDisabledMeansNeverRefreshed(): void
    {
        $moduleManager = $this->createMock(Manager::class);
        $moduleManager->method('isEnabled')->willReturn(false);
        $flagFactory = $this->createMock(FlagFactory::class);
        $flagFactory->expects($this->never())->method('create');

        $viewModel = new StatisticsFreshness(
            $flagFactory,
            $moduleManager,
            $this->scopeConfig,
            $this->createMock(TimezoneInterface::class)
        );

        $this->assertFalse($viewModel->isRefreshed());
    }
}
