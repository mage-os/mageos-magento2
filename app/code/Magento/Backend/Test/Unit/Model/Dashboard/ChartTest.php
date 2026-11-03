<?php
/**
 * Copyright 2020 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Model\Dashboard;

use Magento\Backend\Helper\Dashboard\Order as OrderHelper;
use Magento\Backend\Model\Dashboard\Chart;
use Magento\Backend\Model\Dashboard\Chart\Date as DateRetriever;
use Magento\Backend\Model\Dashboard\Config;
use Magento\Backend\Model\Dashboard\Period;
use Magento\Backend\Model\Dashboard\StatisticsCache;
use Magento\Framework\DataObject;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Reports\Model\ResourceModel\Order\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ChartTest extends TestCase
{
    /**
     * @var Chart
     */
    private $model;

    /**
     * @var ObjectManager
     */
    private $objectManagerHelper;

    /**
     * @var DateRetriever|MockObject
     */
    private $dateRetrieverMock;

    /**
     * @var OrderHelper|MockObject
     */
    private $orderHelperMock;

    /**
     * @var Collection|MockObject
     */
    private $collectionMock;

    /**
     * @inheritDoc
     */
    /**
     * @var StatisticsCache|MockObject
     */
    private $statisticsCacheMock;

    protected function setUp(): void
    {
        $this->objectManagerHelper = new ObjectManager($this);

        $this->dateRetrieverMock = $this->createMock(DateRetriever::class);

        $this->orderHelperMock = $this->createMock(OrderHelper::class);

        $this->collectionMock = $this->createMock(Collection::class);
        $this->orderHelperMock->method('getCollection')
            ->willReturn($this->collectionMock);

        $period = $this->objectManagerHelper->getObject(Period::class);

        $this->statisticsCacheMock = $this->createMock(StatisticsCache::class);
        $this->statisticsCacheMock->method('get')
            ->willReturnCallback(fn ($key, $scope, $lifetime, $loader) => $loader());
        $config = $this->createMock(Config::class);
        $config->method('getTotalsCacheLifetime')->willReturn(300);

        $this->model = $this->objectManagerHelper->getObject(
            Chart::class,
            [
                'dateRetriever' => $this->dateRetrieverMock,
                'orderHelper' => $this->orderHelperMock,
                'period' => $period,
                'statisticsCache' => $this->statisticsCacheMock,
                'dashboardConfig' => $config
            ]
        );
    }

    /**
     * @param string $period
     * @param string $chartParam
     * @param array $result
     *
     * @return void
     */
    #[DataProvider('getByPeriodDataProvider')]
    public function testGetByPeriod(string $period, string $chartParam, array $result): void
    {
        $this->orderHelperMock
            ->method('setParam')
            ->willReturnCallback(function ($arg1, $arg2) use ($period) {
                if ($arg1 == 'period' && $arg2 == $period) {
                    return $this;
                } elseif ($arg1 == 'store' || $arg1=='website' || $arg1 == 'group') {
                    return $this;
                }
            });

        $this->dateRetrieverMock->expects($this->once())
            ->method('getByPeriod')
            ->with($period)
            ->willReturn(array_map(static function ($item) {
                return $item['x'];
            }, $result));

        $this->collectionMock->method('count')
            ->willReturn(2);

        $valueMap = [];
        foreach ($result as $resultItem) {
            $dataObjectMock = new DataObject(['quantity' => $resultItem['y'], 'revenue' => $resultItem['y']]);

            $valueMap[] = [
                'range',
                $resultItem['x'],
                $dataObjectMock
            ];
        }
        $this->collectionMock->method('getItemByColumnValue')
            ->willReturnMap($valueMap);

        $this->assertEquals(
            $result,
            $this->model->getByPeriod($period, $chartParam)
        );
    }

    public function testGetSeriesByPeriodReturnsBothSeriesFromOneLoad(): void
    {
        $this->dateRetrieverMock->expects($this->once())
            ->method('getByPeriod')
            ->with(Period::PERIOD_7_DAYS)
            ->willReturn(['2020-01-21', '2020-01-22']);
        $this->orderHelperMock->expects($this->once())->method('getCollection');
        $this->collectionMock->method('count')->willReturn(1);
        $this->collectionMock->method('getItemByColumnValue')->willReturnMap([
            ['range', '2020-01-21', null],
            ['range', '2020-01-22', new DataObject(['quantity' => '3', 'revenue' => '120.5'])],
        ]);

        $series = $this->model->getSeriesByPeriod(Period::PERIOD_7_DAYS, '1');

        $this->assertSame(Period::PERIOD_7_DAYS, $series['period']);
        $this->assertSame([['x' => '2020-01-21', 'y' => 0], ['x' => '2020-01-22', 'y' => 3.0]], $series['quantity']);
        $this->assertSame([['x' => '2020-01-21', 'y' => 0], ['x' => '2020-01-22', 'y' => 120.5]], $series['revenue']);
    }

    public function testUnknownPeriodFallsBackTo24Hours(): void
    {
        $this->dateRetrieverMock->method('getByPeriod')->willReturn([]);
        $this->collectionMock->method('count')->willReturn(0);

        $series = $this->model->getSeriesByPeriod('bogus');

        $this->assertSame(Period::PERIOD_24_HOURS, $series['period']);
        $this->assertSame([], $series['quantity']);
    }

    public function testSeriesAreCachedByPeriodAndScope(): void
    {
        $statisticsCache = $this->createMock(StatisticsCache::class);
        $statisticsCache->expects($this->once())
            ->method('get')
            ->with('chart', ['period' => '1m', 'store' => '', 'website' => '2', 'group' => ''], 300)
            ->willReturn(['period' => '1m', 'quantity' => [['x' => 'a', 'y' => 1]], 'revenue' => []]);
        $this->orderHelperMock->expects($this->never())->method('getCollection');
        $config = $this->createMock(Config::class);
        $config->method('getTotalsCacheLifetime')->willReturn(300);

        $model = $this->objectManagerHelper->getObject(
            Chart::class,
            [
                'dateRetriever' => $this->dateRetrieverMock,
                'orderHelper' => $this->orderHelperMock,
                'period' => $this->objectManagerHelper->getObject(Period::class),
                'statisticsCache' => $statisticsCache,
                'dashboardConfig' => $config
            ]
        );

        $this->assertSame([['x' => 'a', 'y' => 1]], $model->getByPeriod('1m', 'quantity', null, '2'));
    }

    /**
     * @return array
     */
    public static function getByPeriodDataProvider(): array
    {
        return [
            [
                Period::PERIOD_7_DAYS,
                'revenue',
                [
                    [
                        'x' => '2020-01-21',
                        'y' => 0
                    ],
                    [
                        'x' => '2020-01-22',
                        'y' => 2
                    ],
                    [
                        'x' => '2020-01-23',
                        'y' => 0
                    ],
                    [
                        'x' => '2020-01-24',
                        'y' => 7
                    ]
                ]
            ],
            [
                Period::PERIOD_1_MONTH,
                'quantity',
                [
                    [
                        'x' => '2020-01-21',
                        'y' => 0
                    ],
                    [
                        'x' => '2020-01-22',
                        'y' => 2
                    ],
                    [
                        'x' => '2020-01-23',
                        'y' => 0
                    ],
                    [
                        'x' => '2020-01-24',
                        'y' => 7
                    ]
                ]
            ],
            [
                Period::PERIOD_1_YEAR,
                'quantity',
                [
                    [
                        'x' => '2020-01',
                        'y' => 0
                    ],
                    [
                        'x' => '2020-02',
                        'y' => 2
                    ],
                    [
                        'x' => '2020-03',
                        'y' => 0
                    ],
                    [
                        'x' => '2020-04',
                        'y' => 7
                    ]
                ]
            ]
        ];
    }
}
