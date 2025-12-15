<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Config;

use MageOS\Installer\Model\Detector\RedisDetector;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\note;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\text;
use function Laravel\Prompts\warning;

/**
 * Collects Redis configuration with Laravel Prompts
 */
class RedisConfig
{
    public function __construct(
        private readonly RedisDetector $redisDetector
    ) {
    }

    /**
     * Collect Redis configuration
     *
     * @return array{
     *     session: array{enabled: bool, host: string, port: int, database: int}|null,
     *     cache: array{enabled: bool, host: string, port: int, database: int}|null,
     *     fpc: array{enabled: bool, host: string, port: int, database: int}|null
     * }
     */
    public function collect(): array
    {
        note('Redis Configuration');

        // Detect Redis instances
        $detected = spin(
            message: 'Detecting Redis instances...',
            callback: fn () => $this->redisDetector->detect()
        );

        if (empty($detected)) {
            warning('Redis not detected. Skipping Redis configuration.');
            info('You can configure Redis manually later in app/etc/env.php');

            return [
                'session' => null,
                'cache' => null,
                'fpc' => null
            ];
        }

        $primaryRedis = $detected[0];
        info(sprintf('✓ Detected Redis on %s:%d', $primaryRedis['host'], $primaryRedis['port']));

        // Ask if user wants to use Redis for all purposes
        $useAll = confirm(
            label: 'Use Redis for sessions, cache, and FPC?',
            default: true,
            hint: 'Quick setup with separate databases (db0, db1, db2)'
        );

        if ($useAll) {
            info('✓ Using Redis for all caching purposes (sessions: db0, cache: db1, FPC: db2)');
            return [
                'session' => [
                    'enabled' => true,
                    'host' => $primaryRedis['host'],
                    'port' => $primaryRedis['port'],
                    'database' => 0
                ],
                'cache' => [
                    'enabled' => true,
                    'host' => $primaryRedis['host'],
                    'port' => $primaryRedis['port'],
                    'database' => 1
                ],
                'fpc' => [
                    'enabled' => true,
                    'host' => $primaryRedis['host'],
                    'port' => $primaryRedis['port'],
                    'database' => 2
                ]
            ];
        }

        info('Configure Redis individually:');

        // Individual configuration
        $sessionConfig = $this->collectRedisBackend('session', 0, $primaryRedis);
        $cacheConfig = $this->collectRedisBackend('cache', 1, $primaryRedis);
        $fpcConfig = $this->collectRedisBackend('FPC', 2, $primaryRedis);

        return [
            'session' => $sessionConfig,
            'cache' => $cacheConfig,
            'fpc' => $fpcConfig
        ];
    }

    /**
     * Collect configuration for specific Redis backend
     *
     * @param string $purpose
     * @param int $defaultDb
     * @param array{host: string, port: int} $defaultRedis
     * @return array{enabled: bool, host: string, port: int, database: int}|null
     */
    private function collectRedisBackend(string $purpose, int $defaultDb, array $defaultRedis): ?array
    {
        $enabled = confirm(
            label: sprintf('Use Redis for %s?', $purpose),
            default: true
        );

        if (!$enabled) {
            return null;
        }

        $host = text(
            label: sprintf('Redis %s host', $purpose),
            default: $defaultRedis['host'],
            placeholder: $defaultRedis['host']
        );

        $port = (int)text(
            label: sprintf('Redis %s port', $purpose),
            default: (string)$defaultRedis['port'],
            placeholder: (string)$defaultRedis['port']
        );

        $database = (int)text(
            label: sprintf('Redis %s database', $purpose),
            default: (string)$defaultDb,
            placeholder: (string)$defaultDb
        );

        return [
            'enabled' => true,
            'host' => $host,
            'port' => $port,
            'database' => $database
        ];
    }
}
