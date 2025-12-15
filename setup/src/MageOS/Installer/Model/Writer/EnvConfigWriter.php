<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Writer;

use Magento\Framework\App\DeploymentConfig\Writer;
use Magento\Framework\Config\File\ConfigFilePool;

/**
 * Writes configuration to env.php
 */
class EnvConfigWriter
{
    public function __construct(
        private readonly Writer $writer
    ) {
    }

    /**
     * Write Redis configuration to env.php
     *
     * Accepts both flat VO format and nested collector format
     *
     * @param array<string, mixed> $redisConfig
     * @return void
     * @throws \Exception
     */
    public function writeRedisConfig(array $redisConfig): void
    {
        $config = [];

        // Detect format: flat (from VO) or nested (from collector)
        $isFlat = isset($redisConfig['session']) && is_bool($redisConfig['session']);

        if ($isFlat) {
            // Flat format from RedisConfiguration::toArray()
            $host = $redisConfig['host'] ?? '127.0.0.1';
            $port = $redisConfig['port'] ?? 6379;

            // Session configuration
            if ($redisConfig['session']) {
                $config['session'] = [
                    'save' => 'redis',
                    'redis' => [
                        'host' => $host,
                        'port' => (string)$port,
                        'timeout' => '2.5',
                        'persistent_identifier' => '',
                        'database' => (string)($redisConfig['sessionDb'] ?? 0),
                        'compression_threshold' => '2048',
                        'compression_library' => 'gzip',
                        'log_level' => '4',
                        'max_concurrency' => '6',
                        'break_after_frontend' => '5',
                        'break_after_adminhtml' => '30',
                        'first_lifetime' => '600',
                        'bot_first_lifetime' => '60',
                        'bot_lifetime' => '7200',
                        'disable_locking' => '0',
                        'min_lifetime' => '60',
                        'max_lifetime' => '2592000'
                    ]
                ];
            }

            // Cache configuration
            if ($redisConfig['cache']) {
                $config['cache'] = [
                    'frontend' => [
                        'default' => [
                            'backend' => 'Cm_Cache_Backend_Redis',
                            'backend_options' => [
                                'server' => $host,
                                'port' => (string)$port,
                                'persistent' => '',
                                'database' => (string)($redisConfig['cacheDb'] ?? 1),
                                'password' => '',
                                'force_standalone' => '0',
                                'connect_retries' => '1',
                                'read_timeout' => '10',
                                'automatic_cleaning_factor' => '0',
                                'compress_data' => '1',
                                'compress_tags' => '1',
                                'compress_threshold' => '20480',
                                'compression_lib' => 'gzip',
                                'use_lua' => '0'
                            ]
                        ]
                    ]
                ];
            }

            // FPC configuration
            if ($redisConfig['fpc']) {
                if (!isset($config['cache'])) {
                    $config['cache'] = ['frontend' => []];
                }
                $config['cache']['frontend']['page_cache'] = [
                    'backend' => 'Cm_Cache_Backend_Redis',
                    'backend_options' => [
                        'server' => $host,
                        'port' => (string)$port,
                        'persistent' => '',
                        'database' => (string)($redisConfig['fpcDb'] ?? 2),
                        'password' => '',
                        'force_standalone' => '0',
                        'connect_retries' => '1',
                        'read_timeout' => '10',
                        'automatic_cleaning_factor' => '0',
                        'compress_data' => '1',
                        'compress_tags' => '1',
                        'compress_threshold' => '20480',
                        'compression_lib' => 'gzip',
                        'use_lua' => '0'
                    ]
                ];
            }
        } else {
            // Nested format from RedisConfig::collect() (legacy)
            // Session configuration
            if ($redisConfig['session'] && $redisConfig['session']['enabled']) {
                $config['session'] = [
                    'save' => 'redis',
                    'redis' => [
                        'host' => $redisConfig['session']['host'],
                        'port' => $redisConfig['session']['port'],
                        'timeout' => '2.5',
                        'persistent_identifier' => '',
                        'database' => $redisConfig['session']['database'],
                        'compression_threshold' => '2048',
                        'compression_library' => 'gzip',
                        'log_level' => '4',
                        'max_concurrency' => '6',
                        'break_after_frontend' => '5',
                        'break_after_adminhtml' => '30',
                        'first_lifetime' => '600',
                        'bot_first_lifetime' => '60',
                        'bot_lifetime' => '7200',
                        'disable_locking' => '0',
                        'min_lifetime' => '60',
                        'max_lifetime' => '2592000'
                    ]
                ];
            }

            // Cache configuration
            if ($redisConfig['cache'] && $redisConfig['cache']['enabled']) {
                $config['cache'] = [
                    'frontend' => [
                        'default' => [
                            'backend' => 'Cm_Cache_Backend_Redis',
                            'backend_options' => [
                                'server' => $redisConfig['cache']['host'],
                                'port' => $redisConfig['cache']['port'],
                                'persistent' => '',
                                'database' => $redisConfig['cache']['database'],
                                'password' => '',
                                'force_standalone' => '0',
                                'connect_retries' => '1',
                                'read_timeout' => '10',
                                'automatic_cleaning_factor' => '0',
                                'compress_data' => '1',
                                'compress_tags' => '1',
                                'compress_threshold' => '20480',
                                'compression_lib' => 'gzip',
                                'use_lua' => '0'
                            ]
                        ]
                    ]
                ];

                // FPC configuration
                if ($redisConfig['fpc'] && $redisConfig['fpc']['enabled']) {
                    $config['cache']['frontend']['page_cache'] = [
                        'backend' => 'Cm_Cache_Backend_Redis',
                        'backend_options' => [
                            'server' => $redisConfig['fpc']['host'],
                            'port' => $redisConfig['fpc']['port'],
                            'persistent' => '',
                            'database' => $redisConfig['fpc']['database'],
                            'password' => '',
                            'force_standalone' => '0',
                            'connect_retries' => '1',
                            'read_timeout' => '10',
                            'automatic_cleaning_factor' => '0',
                            'compress_data' => '1',
                            'compress_tags' => '1',
                            'compress_threshold' => '20480',
                            'compression_lib' => 'gzip',
                            'use_lua' => '0'
                        ]
                    ];
                }
            }
        }

        if (!empty($config)) {
            $this->writer->saveConfig([ConfigFilePool::APP_ENV => $config], true);
        }
    }

    /**
     * Write RabbitMQ configuration to env.php
     *
     * @param array<string, mixed> $rabbitMqConfig
     * @return void
     * @throws \Exception
     */
    public function writeRabbitMQConfig(array $rabbitMqConfig): void
    {
        if (!$rabbitMqConfig || !$rabbitMqConfig['enabled']) {
            return;
        }

        // Handle both 'virtualHost' (from VO) and 'virtualhost' (from collector) formats
        $virtualHost = $rabbitMqConfig['virtualHost'] ?? $rabbitMqConfig['virtualhost'] ?? '/';

        $config = [
            'queue' => [
                'amqp' => [
                    'host' => $rabbitMqConfig['host'],
                    'port' => $rabbitMqConfig['port'],
                    'user' => $rabbitMqConfig['user'],
                    'password' => $rabbitMqConfig['password'],
                    'virtualhost' => $virtualHost
                ]
            ]
        ];

        $this->writer->saveConfig([ConfigFilePool::APP_ENV => $config], true);
    }
}
