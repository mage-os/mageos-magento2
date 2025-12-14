<?php

declare(strict_types=1);

namespace MageOS\Installer\Test\Unit\MageOS\Installer\Model\Writer;

use Magento\Framework\App\DeploymentConfig\Writer;
use Magento\Framework\Config\File\ConfigFilePool;
use MageOS\Installer\Model\Writer\EnvConfigWriter;
use MageOS\Installer\Test\Util\TestDataBuilder;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for EnvConfigWriter
 *
 * Tests env.php modification for Redis and RabbitMQ configuration
 */
final class EnvConfigWriterTest extends TestCase
{
    private Writer $writerMock;
    private EnvConfigWriter $envWriter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->writerMock = $this->createMock(Writer::class);
        $this->envWriter = new EnvConfigWriter($this->writerMock);
    }

    public function test_write_redis_config_with_session_only(): void
    {
        $redisConfig = [
            'session' => true,
            'cache' => false,
            'fpc' => false,
            'host' => 'redis.local',
            'port' => 6379,
            'sessionDb' => 0
        ];

        $this->writerMock->expects($this->once())
            ->method('saveConfig')
            ->with(
                $this->callback(function ($config) {
                    $envConfig = $config[ConfigFilePool::APP_ENV];
                    return isset($envConfig['session'])
                        && $envConfig['session']['save'] === 'redis'
                        && $envConfig['session']['redis']['host'] === 'redis.local'
                        && $envConfig['session']['redis']['database'] === '0';
                }),
                true // merge=true
            );

        $this->envWriter->writeRedisConfig($redisConfig);
    }

    public function test_write_redis_config_with_cache_only(): void
    {
        $redisConfig = [
            'session' => false,
            'cache' => true,
            'fpc' => false,
            'host' => '127.0.0.1',
            'port' => 6379,
            'cacheDb' => 1
        ];

        $this->writerMock->expects($this->once())
            ->method('saveConfig')
            ->with(
                $this->callback(function ($config) {
                    $envConfig = $config[ConfigFilePool::APP_ENV];
                    return isset($envConfig['cache']['frontend']['default'])
                        && $envConfig['cache']['frontend']['default']['backend'] === 'Cm_Cache_Backend_Redis'
                        && $envConfig['cache']['frontend']['default']['backend_options']['database'] === '1';
                }),
                true
            );

        $this->envWriter->writeRedisConfig($redisConfig);
    }

    public function test_write_redis_config_with_fpc_only(): void
    {
        $redisConfig = [
            'session' => false,
            'cache' => false,
            'fpc' => true,
            'host' => 'localhost',
            'port' => 6379,
            'fpcDb' => 2
        ];

        $this->writerMock->expects($this->once())
            ->method('saveConfig')
            ->with(
                $this->callback(function ($config) {
                    $envConfig = $config[ConfigFilePool::APP_ENV];
                    return isset($envConfig['cache']['frontend']['page_cache'])
                        && $envConfig['cache']['frontend']['page_cache']['backend'] === 'Cm_Cache_Backend_Redis'
                        && $envConfig['cache']['frontend']['page_cache']['backend_options']['database'] === '2';
                }),
                true
            );

        $this->envWriter->writeRedisConfig($redisConfig);
    }

    public function test_write_redis_config_with_all_features_enabled(): void
    {
        $redisConfig = [
            'session' => true,
            'cache' => true,
            'fpc' => true,
            'host' => 'redis.example.com',
            'port' => 6380,
            'sessionDb' => 0,
            'cacheDb' => 1,
            'fpcDb' => 2
        ];

        $this->writerMock->expects($this->once())
            ->method('saveConfig')
            ->with(
                $this->callback(function ($config) {
                    $envConfig = $config[ConfigFilePool::APP_ENV];
                    return isset($envConfig['session'])
                        && isset($envConfig['cache']['frontend']['default'])
                        && isset($envConfig['cache']['frontend']['page_cache']);
                }),
                true
            );

        $this->envWriter->writeRedisConfig($redisConfig);
    }

    public function test_write_redis_config_uses_correct_database_numbers(): void
    {
        $redisConfig = [
            'session' => true,
            'cache' => true,
            'fpc' => true,
            'host' => 'localhost',
            'port' => 6379,
            'sessionDb' => 5,
            'cacheDb' => 6,
            'fpcDb' => 7
        ];

        $this->writerMock->expects($this->once())
            ->method('saveConfig')
            ->with(
                $this->callback(function ($config) {
                    $envConfig = $config[ConfigFilePool::APP_ENV];
                    return $envConfig['session']['redis']['database'] === '5'
                        && $envConfig['cache']['frontend']['default']['backend_options']['database'] === '6'
                        && $envConfig['cache']['frontend']['page_cache']['backend_options']['database'] === '7';
                }),
                true
            );

        $this->envWriter->writeRedisConfig($redisConfig);
    }

    public function test_write_redis_config_converts_port_to_string(): void
    {
        $redisConfig = [
            'session' => true,
            'cache' => false,
            'fpc' => false,
            'host' => 'localhost',
            'port' => 6380, // int
            'sessionDb' => 0
        ];

        $this->writerMock->expects($this->once())
            ->method('saveConfig')
            ->with(
                $this->callback(function ($config) {
                    $envConfig = $config[ConfigFilePool::APP_ENV];
                    return $envConfig['session']['redis']['port'] === '6380'
                        && is_string($envConfig['session']['redis']['port']);
                }),
                true
            );

        $this->envWriter->writeRedisConfig($redisConfig);
    }

    public function test_write_redis_config_with_nested_format(): void
    {
        $redisConfig = [
            'session' => [
                'enabled' => true,
                'host' => 'redis.local',
                'port' => '6379',
                'database' => '0'
            ],
            'cache' => [
                'enabled' => false
            ],
            'fpc' => [
                'enabled' => false
            ]
        ];

        $this->writerMock->expects($this->once())
            ->method('saveConfig')
            ->with(
                $this->callback(function ($config) {
                    $envConfig = $config[ConfigFilePool::APP_ENV];
                    return isset($envConfig['session'])
                        && $envConfig['session']['redis']['host'] === 'redis.local';
                }),
                true
            );

        $this->envWriter->writeRedisConfig($redisConfig);
    }

    public function test_write_redis_config_doesnt_write_when_all_disabled(): void
    {
        $redisConfig = [
            'session' => false,
            'cache' => false,
            'fpc' => false,
            'host' => 'localhost',
            'port' => 6379
        ];

        // Should not call saveConfig when nothing enabled
        $this->writerMock->expects($this->never())
            ->method('saveConfig');

        $this->envWriter->writeRedisConfig($redisConfig);
    }

    public function test_write_rabbitmq_config_when_enabled(): void
    {
        $rabbitMqConfig = [
            'enabled' => true,
            'host' => 'rabbitmq.local',
            'port' => 5672,
            'user' => 'magento',
            'password' => 'secure_pass',
            'virtualHost' => '/production'
        ];

        $this->writerMock->expects($this->once())
            ->method('saveConfig')
            ->with(
                $this->callback(function ($config) {
                    $envConfig = $config[ConfigFilePool::APP_ENV];
                    return isset($envConfig['queue']['amqp'])
                        && $envConfig['queue']['amqp']['host'] === 'rabbitmq.local'
                        && $envConfig['queue']['amqp']['port'] === 5672
                        && $envConfig['queue']['amqp']['user'] === 'magento'
                        && $envConfig['queue']['amqp']['password'] === 'secure_pass'
                        && $envConfig['queue']['amqp']['virtualhost'] === '/production';
                }),
                true
            );

        $this->envWriter->writeRabbitMQConfig($rabbitMqConfig);
    }

    public function test_write_rabbitmq_config_handles_lowercase_virtualhost(): void
    {
        $rabbitMqConfig = [
            'enabled' => true,
            'host' => 'localhost',
            'port' => 5672,
            'user' => 'guest',
            'password' => 'guest',
            'virtualhost' => '/magento' // lowercase
        ];

        $this->writerMock->expects($this->once())
            ->method('saveConfig')
            ->with(
                $this->callback(function ($config) {
                    $envConfig = $config[ConfigFilePool::APP_ENV];
                    return $envConfig['queue']['amqp']['virtualhost'] === '/magento';
                }),
                true
            );

        $this->envWriter->writeRabbitMQConfig($rabbitMqConfig);
    }

    public function test_write_rabbitmq_config_prefers_camelcase_virtualhost(): void
    {
        $rabbitMqConfig = [
            'enabled' => true,
            'host' => 'localhost',
            'port' => 5672,
            'user' => 'guest',
            'password' => 'guest',
            'virtualHost' => '/prod',
            'virtualhost' => '/dev' // both present
        ];

        $this->writerMock->expects($this->once())
            ->method('saveConfig')
            ->with(
                $this->callback(function ($config) {
                    $envConfig = $config[ConfigFilePool::APP_ENV];
                    return $envConfig['queue']['amqp']['virtualhost'] === '/prod';
                }),
                true
            );

        $this->envWriter->writeRabbitMQConfig($rabbitMqConfig);
    }

    public function test_write_rabbitmq_config_doesnt_write_when_disabled(): void
    {
        $rabbitMqConfig = [
            'enabled' => false,
            'host' => 'localhost',
            'port' => 5672,
            'user' => 'guest',
            'password' => 'guest'
        ];

        $this->writerMock->expects($this->never())
            ->method('saveConfig');

        $this->envWriter->writeRabbitMQConfig($rabbitMqConfig);
    }

    public function test_write_rabbitmq_config_doesnt_write_when_empty_array(): void
    {
        $rabbitMqConfig = [];

        $this->writerMock->expects($this->never())
            ->method('saveConfig');

        $this->envWriter->writeRabbitMQConfig($rabbitMqConfig);
    }

    public function test_write_redis_config_uses_merge_mode(): void
    {
        $redisConfig = TestDataBuilder::validRedisConfig()->toArray();

        $this->writerMock->expects($this->once())
            ->method('saveConfig')
            ->with(
                $this->anything(),
                true // This is the merge parameter - critical!
            );

        $this->envWriter->writeRedisConfig($redisConfig);
    }

    public function test_write_rabbitmq_config_uses_merge_mode(): void
    {
        $rabbitMqConfig = TestDataBuilder::validRabbitMQConfig()->toArray();

        $this->writerMock->expects($this->once())
            ->method('saveConfig')
            ->with(
                $this->anything(),
                true // This is the merge parameter - critical!
            );

        $this->envWriter->writeRabbitMQConfig($rabbitMqConfig);
    }
}
