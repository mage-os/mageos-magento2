<?php

declare(strict_types=1);

namespace MageOS\Installer\Test\Unit\MageOS\Installer\Model\VO;

use MageOS\Installer\Model\VO\RedisConfiguration;
use MageOS\Installer\Test\TestCase\AbstractVOTest;

/**
 * Unit tests for RedisConfiguration VO
 */
class RedisConfigurationTest extends AbstractVOTest
{
    protected function createValidInstance(): RedisConfiguration
    {
        return new RedisConfiguration(
            session: true,
            cache: true,
            fpc: true,
            host: '127.0.0.1',
            port: 6379,
            sessionDb: 0,
            cacheDb: 1,
            fpcDb: 2
        );
    }

    protected function getSensitiveFields(): array
    {
        return []; // No sensitive fields
    }

    public function testItConstructsWithAllParameters(): void
    {
        $config = new RedisConfiguration(
            session: true,
            cache: false,
            fpc: true,
            host: 'redis.local',
            port: 6380,
            sessionDb: 0,
            cacheDb: 3,
            fpcDb: 4
        );

        $this->assertPropertyEquals($config, 'session', true);
        $this->assertPropertyEquals($config, 'cache', false);
        $this->assertPropertyEquals($config, 'fpc', true);
        $this->assertPropertyEquals($config, 'host', 'redis.local');
        $this->assertPropertyEquals($config, 'port', 6380);
        $this->assertPropertyEquals($config, 'sessionDb', 0);
        $this->assertPropertyEquals($config, 'cacheDb', 3);
        $this->assertPropertyEquals($config, 'fpcDb', 4);
    }

    public function testItConstructsWithDefaults(): void
    {
        $config = new RedisConfiguration(
            session: false,
            cache: false,
            fpc: false
        );

        $this->assertPropertyEquals($config, 'host', '127.0.0.1');
        $this->assertPropertyEquals($config, 'port', 6379);
        $this->assertPropertyEquals($config, 'sessionDb', 0);
        $this->assertPropertyEquals($config, 'cacheDb', 1);
        $this->assertPropertyEquals($config, 'fpcDb', 2);
    }

    public function testIsEnabledReturnsTrueWhenAnyFeatureEnabled(): void
    {
        $testCases = [
            [true, false, false, true],  // session only
            [false, true, false, true],  // cache only
            [false, false, true, true],  // fpc only
            [true, true, true, true],    // all
            [false, false, false, false] // none
        ];

        foreach ($testCases as [$session, $cache, $fpc, $expected]) {
            $config = new RedisConfiguration(
                session: $session,
                cache: $cache,
                fpc: $fpc
            );

            $this->assertEquals(
                $expected,
                $config->isEnabled(),
                "isEnabled() should return {$expected} for session={$session}, cache={$cache}, fpc={$fpc}"
            );
        }
    }

    public function testFromArrayWithFlatFormat(): void
    {
        $data = [
            'session' => true,
            'cache' => false,
            'fpc' => true,
            'host' => 'redis.test',
            'port' => 6380,
            'sessionDb' => 0,
            'cacheDb' => 5,
            'fpcDb' => 6
        ];

        $config = RedisConfiguration::fromArray($data);

        $this->assertPropertyEquals($config, 'session', true);
        $this->assertPropertyEquals($config, 'cache', false);
        $this->assertPropertyEquals($config, 'fpc', true);
        $this->assertPropertyEquals($config, 'host', 'redis.test');
        $this->assertPropertyEquals($config, 'port', 6380);
        $this->assertPropertyEquals($config, 'sessionDb', 0);
        $this->assertPropertyEquals($config, 'cacheDb', 5);
        $this->assertPropertyEquals($config, 'fpcDb', 6);
    }

    public function testFromArrayWithNestedFormat(): void
    {
        $data = [
            'session' => [
                'enabled' => true,
                'host' => 'redis.local',
                'port' => 6379,
                'database' => 0
            ],
            'cache' => [
                'enabled' => true,
                'host' => 'redis.local',
                'port' => 6379,
                'database' => 1
            ],
            'fpc' => [
                'enabled' => false
            ]
        ];

        $config = RedisConfiguration::fromArray($data);

        $this->assertPropertyEquals($config, 'session', true);
        $this->assertPropertyEquals($config, 'cache', true);
        $this->assertPropertyEquals($config, 'fpc', false);
        $this->assertPropertyEquals($config, 'host', 'redis.local');
        $this->assertPropertyEquals($config, 'port', 6379);
        $this->assertPropertyEquals($config, 'sessionDb', 0);
        $this->assertPropertyEquals($config, 'cacheDb', 1);
    }

    public function testFromArrayNestedFormatUsesFirstAvailableHost(): void
    {
        // When features have different hosts, use first available
        $data = [
            'session' => ['enabled' => false],
            'cache' => ['enabled' => true, 'host' => 'cache.redis'],
            'fpc' => ['enabled' => true, 'host' => 'fpc.redis']
        ];

        $config = RedisConfiguration::fromArray($data);

        $this->assertPropertyEquals($config, 'host', 'cache.redis');
    }

    public function testFromArrayWithMissingFieldsUsesDefaults(): void
    {
        $data = [];

        $config = RedisConfiguration::fromArray($data);

        $this->assertPropertyEquals($config, 'session', false);
        $this->assertPropertyEquals($config, 'cache', false);
        $this->assertPropertyEquals($config, 'fpc', false);
        $this->assertPropertyEquals($config, 'host', '127.0.0.1');
        $this->assertPropertyEquals($config, 'port', 6379);
        $this->assertPropertyEquals($config, 'sessionDb', 0);
        $this->assertPropertyEquals($config, 'cacheDb', 1);
        $this->assertPropertyEquals($config, 'fpcDb', 2);
    }

    public function testFromArrayCoercesPortToInt(): void
    {
        $data = [
            'session' => true,
            'cache' => false,
            'fpc' => false,
            'port' => '6380' // string
        ];

        $config = RedisConfiguration::fromArray($data);

        $this->assertPropertyEquals($config, 'port', 6380);
        $this->assertIsInt($config->port);
    }

    public function testToArrayContainsAllFields(): void
    {
        $config = $this->createValidInstance();
        $array = $config->toArray();

        $this->assertArrayHasKey('session', $array);
        $this->assertArrayHasKey('cache', $array);
        $this->assertArrayHasKey('fpc', $array);
        $this->assertArrayHasKey('host', $array);
        $this->assertArrayHasKey('port', $array);
        $this->assertArrayHasKey('sessionDb', $array);
        $this->assertArrayHasKey('cacheDb', $array);
        $this->assertArrayHasKey('fpcDb', $array);
    }
}
