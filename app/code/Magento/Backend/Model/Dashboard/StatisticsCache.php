<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Backend\Model\Dashboard;

use Magento\Framework\App\CacheInterface;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Cache for expensive dashboard aggregate figures (lifetime sales, period totals, attention counts)
 *
 * Entries are keyed by a logical name plus the scope parameters that influence the figures
 * (store/website/group/period). Lifetime 0 disables caching so figures are always queried live.
 */
class StatisticsCache
{
    public const CACHE_TAG = 'BACKEND_DASHBOARD';

    private const KEY_PREFIX = 'backend_dashboard_';

    /**
     * @param CacheInterface $cache
     * @param SerializerInterface $serializer
     */
    public function __construct(
        private readonly CacheInterface $cache,
        private readonly SerializerInterface $serializer
    ) {
    }

    /**
     * Return the cached figures, or compute them via $loader and cache the result
     *
     * @param string $key
     * @param array $scope
     * @param int $lifetime
     * @param callable $loader
     * @return array
     */
    public function get(string $key, array $scope, int $lifetime, callable $loader): array
    {
        if ($lifetime <= 0) {
            return $loader();
        }
        $data = $this->load($key, $scope);
        if ($data === null) {
            $data = $loader();
            $this->save($key, $scope, $data, $lifetime);
        }
        return $data;
    }

    /**
     * Return cached figures without computing them, or null when absent
     *
     * @param string $key
     * @param array $scope
     * @return array|null
     */
    public function load(string $key, array $scope): ?array
    {
        $raw = $this->cache->load($this->buildId($key, $scope));
        if ($raw === false || $raw === null || $raw === '') {
            return null;
        }
        $data = $this->serializer->unserialize($raw);
        return is_array($data) ? $data : null;
    }

    /**
     * Store figures
     *
     * @param string $key
     * @param array $scope
     * @param array $data
     * @param int $lifetime
     * @return void
     */
    public function save(string $key, array $scope, array $data, int $lifetime): void
    {
        if ($lifetime <= 0) {
            return;
        }
        $this->cache->save(
            $this->serializer->serialize($data),
            $this->buildId($key, $scope),
            [self::CACHE_TAG],
            $lifetime
        );
    }

    /**
     * Drop every cached dashboard figure
     *
     * @return void
     */
    public function clean(): void
    {
        $this->cache->clean([self::CACHE_TAG]);
    }

    /**
     * Build the cache identifier for a key and scope
     *
     * @param string $key
     * @param array $scope
     * @return string
     */
    private function buildId(string $key, array $scope): string
    {
        ksort($scope);
        return self::KEY_PREFIX . $key . '_' . sha1($this->serializer->serialize($scope));
    }
}
