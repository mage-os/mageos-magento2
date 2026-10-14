<?php
/**
 * Copyright 2020 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Model\Dashboard;

use Magento\Backend\Helper\Dashboard\Order as OrderHelper;
use Magento\Backend\Model\Dashboard\Chart\Date;
use Magento\Framework\App\ObjectManager;

/**
 * Dashboard chart data retriever
 */
class Chart
{
    public const SERIES_QUANTITY = 'quantity';
    public const SERIES_REVENUE = 'revenue';

    private const CACHE_KEY = 'chart';

    /**
     * @var Date
     */
    private $dateRetriever;

    /**
     * @var OrderHelper
     */
    private $orderHelper;

    /**
     * @var Period
     */
    private $period;

    /**
     * @var StatisticsCache
     */
    private $statisticsCache;

    /**
     * @var Config
     */
    private $dashboardConfig;

    /**
     * @param Date $dateRetriever
     * @param OrderHelper $orderHelper
     * @param Period $period
     * @param StatisticsCache|null $statisticsCache
     * @param Config|null $dashboardConfig
     */
    public function __construct(
        Date $dateRetriever,
        OrderHelper $orderHelper,
        Period $period,
        ?StatisticsCache $statisticsCache = null,
        ?Config $dashboardConfig = null
    ) {
        $this->dateRetriever = $dateRetriever;
        $this->orderHelper = $orderHelper;
        $this->period = $period;
        $this->statisticsCache = $statisticsCache ?? ObjectManager::getInstance()->get(StatisticsCache::class);
        $this->dashboardConfig = $dashboardConfig ?? ObjectManager::getInstance()->get(Config::class);
    }

    /**
     * Get data for dashboard chart
     *
     * @param string $period
     * @param string $chartParam
     * @param string|null $store
     * @param string|null $website
     * @param string|null $group
     * @return array
     */
    public function getByPeriod(
        string $period,
        string $chartParam,
        ?string $store = null,
        ?string $website = null,
        ?string $group = null
    ): array {
        $series = $this->getSeriesByPeriod($period, $store, $website, $group);

        return $series[$chartParam] ?? [];
    }

    /**
     * Order count and revenue per range bucket for the period, from a single (cached) query
     *
     * @param string $period
     * @param string|null $store
     * @param string|null $website
     * @param string|null $group
     * @return array{period: string, quantity: array, revenue: array}
     */
    public function getSeriesByPeriod(
        string $period,
        ?string $store = null,
        ?string $website = null,
        ?string $group = null
    ): array {
        $period = $this->resolvePeriod($period);

        return $this->statisticsCache->get(
            self::CACHE_KEY,
            [
                'period' => $period,
                'store' => (string)$store,
                'website' => (string)$website,
                'group' => (string)$group,
            ],
            $this->dashboardConfig->getTotalsCacheLifetime(),
            fn (): array => $this->loadSeries($period, $store, $website, $group)
        );
    }

    /**
     * Query both series for the period
     *
     * @param string $period
     * @param string|null $store
     * @param string|null $website
     * @param string|null $group
     * @return array{period: string, quantity: array, revenue: array}
     */
    private function loadSeries(string $period, ?string $store, ?string $website, ?string $group): array
    {
        $this->orderHelper->setParam('store', $store);
        $this->orderHelper->setParam('website', $website);
        $this->orderHelper->setParam('group', $group);
        $this->orderHelper->setParam('period', $period);

        $dates = $this->dateRetriever->getByPeriod($period);
        $collection = $this->orderHelper->getCollection();

        $series = ['period' => $period, self::SERIES_QUANTITY => [], self::SERIES_REVENUE => []];

        if ($collection->count() > 0) {
            foreach ($dates as $date) {
                $item = $collection->getItemByColumnValue('range', $date);
                foreach ([self::SERIES_QUANTITY, self::SERIES_REVENUE] as $name) {
                    $series[$name][] = [
                        'x' => $date,
                        'y' => $item ? (float)$item->getData($name) : 0
                    ];
                }
            }
        }

        return $series;
    }

    /**
     * Fall back to the default period for unknown values
     *
     * @param string $period
     * @return string
     */
    private function resolvePeriod(string $period): string
    {
        $availablePeriods = array_keys($this->period->getDatePeriods());

        return $period && in_array($period, $availablePeriods, false) ? $period : Period::PERIOD_24_HOURS;
    }
}
