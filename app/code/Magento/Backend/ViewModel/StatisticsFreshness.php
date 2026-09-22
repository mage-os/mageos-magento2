<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Backend\ViewModel;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Module\Manager;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Reports\Model\Flag;
use Magento\Reports\Model\FlagFactory;

/**
 * When the aggregated report statistics behind the dashboard were last refreshed
 *
 * Reads the single-row report flags, so it is cheap regardless of store size.
 */
class StatisticsFreshness implements ArgumentInterface
{
    private const XML_PATH_USE_AGGREGATED_DATA = 'sales/dashboard/use_aggregated_data';

    private const STALE_AFTER_HOURS = 24;

    /**
     * @var \DateTimeImmutable|null|false
     */
    private $lastRefreshedAt = false;

    /**
     * @param FlagFactory $flagFactory
     * @param Manager $moduleManager
     * @param ScopeConfigInterface $scopeConfig
     * @param TimezoneInterface $localeDate
     */
    public function __construct(
        private readonly FlagFactory $flagFactory,
        private readonly Manager $moduleManager,
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly TimezoneInterface $localeDate
    ) {
    }

    /**
     * Whether the statistics have ever been aggregated
     *
     * @return bool
     */
    public function isRefreshed(): bool
    {
        return $this->getLastRefreshedAt() !== null;
    }

    /**
     * Last aggregation time of the order statistics, or null if never run
     *
     * @return \DateTimeImmutable|null
     */
    public function getLastRefreshedAt(): ?\DateTimeImmutable
    {
        if ($this->lastRefreshedAt !== false) {
            return $this->lastRefreshedAt;
        }
        $this->lastRefreshedAt = null;
        if ($this->moduleManager->isEnabled('Magento_Reports')) {
            $flag = $this->flagFactory->create()->setReportFlagCode(Flag::REPORT_ORDER_FLAG_CODE)->loadSelf();
            $lastUpdate = $flag->hasData() ? (string)$flag->getLastUpdate() : '';
            if ($lastUpdate !== '') {
                $this->lastRefreshedAt = new \DateTimeImmutable($lastUpdate, new \DateTimeZone('UTC'));
            }
        }
        return $this->lastRefreshedAt;
    }

    /**
     * Last refresh time formatted for the admin locale, or null if never run
     *
     * @return string|null
     */
    public function getLastRefreshedAtFormatted(): ?string
    {
        $date = $this->getLastRefreshedAt();
        if ($date === null) {
            return null;
        }
        return $this->localeDate->formatDateTime($date, \IntlDateFormatter::MEDIUM, \IntlDateFormatter::SHORT);
    }

    /**
     * Whether the dashboard reads the aggregated tables instead of live orders
     *
     * @return bool
     */
    public function isAggregatedMode(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_USE_AGGREGATED_DATA);
    }

    /**
     * Whether the aggregated data is old enough that dashboard figures may be misleading
     *
     * @param int $hours
     * @return bool
     */
    public function isStale(int $hours = self::STALE_AFTER_HOURS): bool
    {
        $date = $this->getLastRefreshedAt();
        if ($date === null) {
            return true;
        }
        return $date->getTimestamp() < time() - $hours * 3600;
    }
}
