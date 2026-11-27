<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Backend\Model\Dashboard;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Admin dashboard configuration accessor
 */
class Config
{
    public const XML_PATH_ENABLE_CHARTS = 'admin/dashboard/enable_charts';
    public const XML_PATH_LIFETIME_CACHE_LIFETIME = 'admin/dashboard/lifetime_cache_lifetime';
    public const XML_PATH_TOTALS_CACHE_LIFETIME = 'admin/dashboard/totals_cache_lifetime';
    public const XML_PATH_LAST_ORDERS_COUNT = 'admin/dashboard/last_orders_count';

    private const LAST_ORDERS_DEFAULT = 5;
    private const LAST_ORDERS_MAX = 20;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    /**
     * Whether dashboard charts are enabled
     *
     * @return bool
     */
    public function isChartsEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLE_CHARTS, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Cache lifetime in seconds for the Lifetime Sales / Average Order figures; 0 disables caching
     *
     * @return int
     */
    public function getLifetimeCacheLifetime(): int
    {
        return max(0, (int)$this->scopeConfig->getValue(self::XML_PATH_LIFETIME_CACHE_LIFETIME));
    }

    /**
     * Cache lifetime in seconds for the period totals bar; 0 disables caching
     *
     * @return int
     */
    public function getTotalsCacheLifetime(): int
    {
        return max(0, (int)$this->scopeConfig->getValue(self::XML_PATH_TOTALS_CACHE_LIFETIME));
    }

    /**
     * Rows shown in the Last Orders panel, clamped to 1..20
     *
     * @return int
     */
    public function getLastOrdersCount(): int
    {
        $count = (int)$this->scopeConfig->getValue(self::XML_PATH_LAST_ORDERS_COUNT);
        return $count > 0 ? min($count, self::LAST_ORDERS_MAX) : self::LAST_ORDERS_DEFAULT;
    }
}
