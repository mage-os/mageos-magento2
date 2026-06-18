<?php

declare(strict_types=1);

namespace MageOS\Installer\Test\Unit\MageOS\Installer\Model\VO;

use MageOS\Installer\Model\VO\RabbitMQConfiguration;
use MageOS\Installer\Test\TestCase\AbstractVOTest;

/**
 * Unit tests for RabbitMQConfiguration VO
 */
class RabbitMQConfigurationTest extends AbstractVOTest
{
    protected function createValidInstance(): RabbitMQConfiguration
    {
        return new RabbitMQConfiguration(
            enabled: true,
            host: 'localhost',
            port: 5672,
            user: 'guest',
            password: 'SecureRabbitPass!',
            virtualHost: '/'
        );
    }

    protected function getSensitiveFields(): array
    {
        return ['password'];
    }

    public function testItConstructsWithAllParameters(): void
    {
        $config = new RabbitMQConfiguration(
            enabled: true,
            host: 'rabbitmq.local',
            port: 5673,
            user: 'admin',
            password: 'AdminPass123',
            virtualHost: '/magento'
        );

        $this->assertPropertyEquals($config, 'enabled', true);
        $this->assertPropertyEquals($config, 'host', 'rabbitmq.local');
        $this->assertPropertyEquals($config, 'port', 5673);
        $this->assertPropertyEquals($config, 'user', 'admin');
        $this->assertPropertyEquals($config, 'password', 'AdminPass123');
        $this->assertPropertyEquals($config, 'virtualHost', '/magento');
    }

    public function testItConstructsWithDefaults(): void
    {
        $config = new RabbitMQConfiguration(enabled: false);

        $this->assertPropertyEquals($config, 'enabled', false);
        $this->assertPropertyEquals($config, 'host', 'localhost');
        $this->assertPropertyEquals($config, 'port', 5672);
        $this->assertPropertyEquals($config, 'user', 'guest');
        $this->assertPropertyEquals($config, 'password', 'guest');
        $this->assertPropertyEquals($config, 'virtualHost', '/');
    }

    public function testToArrayExcludesPasswordByDefault(): void
    {
        $config = $this->createValidInstance();
        $array = $config->toArray();

        $this->assertArrayHasKey('enabled', $array);
        $this->assertArrayHasKey('host', $array);
        $this->assertArrayHasKey('port', $array);
        $this->assertArrayHasKey('user', $array);
        $this->assertArrayHasKey('virtualHost', $array);
        $this->assertArrayNotHasKey('password', $array);
    }

    public function testFromArrayWithCompleteData(): void
    {
        $data = [
            'enabled' => true,
            'host' => 'amqp.test',
            'port' => 5673,
            'user' => 'magento',
            'password' => 'SecurePass',
            'virtualHost' => '/production'
        ];

        $config = RabbitMQConfiguration::fromArray($data);

        $this->assertPropertyEquals($config, 'enabled', true);
        $this->assertPropertyEquals($config, 'host', 'amqp.test');
        $this->assertPropertyEquals($config, 'port', 5673);
        $this->assertPropertyEquals($config, 'user', 'magento');
        $this->assertPropertyEquals($config, 'password', 'SecurePass');
        $this->assertPropertyEquals($config, 'virtualHost', '/production');
    }

    public function testFromArrayWithNullReturnsDisabled(): void
    {
        $config = RabbitMQConfiguration::fromArray(null);

        $this->assertPropertyEquals($config, 'enabled', false);
    }

    public function testFromArrayHandlesLowercaseVirtualhost(): void
    {
        $data = [
            'enabled' => true,
            'virtualhost' => '/magento' // lowercase variant
        ];

        $config = RabbitMQConfiguration::fromArray($data);

        $this->assertPropertyEquals($config, 'virtualHost', '/magento');
    }

    public function testFromArrayPrefersCamelcaseVirtualhost(): void
    {
        $data = [
            'enabled' => true,
            'virtualHost' => '/production',
            'virtualhost' => '/staging' // both present
        ];

        $config = RabbitMQConfiguration::fromArray($data);

        // Should prefer camelCase version
        $this->assertPropertyEquals($config, 'virtualHost', '/production');
    }

    public function testFromArrayWithMissingFieldsUsesDefaults(): void
    {
        $data = ['enabled' => true];

        $config = RabbitMQConfiguration::fromArray($data);

        $this->assertPropertyEquals($config, 'enabled', true);
        $this->assertPropertyEquals($config, 'host', 'localhost');
        $this->assertPropertyEquals($config, 'port', 5672);
        $this->assertPropertyEquals($config, 'user', 'guest');
        $this->assertPropertyEquals($config, 'password', 'guest');
        $this->assertPropertyEquals($config, 'virtualHost', '/');
    }

    public function testFromArrayCoercesPortToInt(): void
    {
        $data = [
            'enabled' => true,
            'port' => '5673' // string
        ];

        $config = RabbitMQConfiguration::fromArray($data);

        $this->assertPropertyEquals($config, 'port', 5673);
        $this->assertIsInt($config->port);
    }
}
