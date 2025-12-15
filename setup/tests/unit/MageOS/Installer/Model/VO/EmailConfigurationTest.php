<?php

declare(strict_types=1);

namespace MageOS\Installer\Test\Unit\MageOS\Installer\Model\VO;

use MageOS\Installer\Model\VO\EmailConfiguration;
use MageOS\Installer\Test\TestCase\AbstractVOTest;

/**
 * Unit tests for EmailConfiguration VO
 */
class EmailConfigurationTest extends AbstractVOTest
{
    protected function createValidInstance(): EmailConfiguration
    {
        return new EmailConfiguration(
            configure: true,
            transport: 'smtp',
            host: 'smtp.example.com',
            port: 587,
            auth: 'login',
            username: 'user@example.com',
            password: 'SecureEmailPass!'
        );
    }

    protected function getSensitiveFields(): array
    {
        return ['password'];
    }

    public function test_it_constructs_with_all_parameters(): void
    {
        $config = new EmailConfiguration(
            configure: true,
            transport: 'smtp',
            host: 'mail.example.com',
            port: 465,
            auth: 'plain',
            username: 'admin@example.com',
            password: 'EmailPass123'
        );

        $this->assertPropertyEquals($config, 'configure', true);
        $this->assertPropertyEquals($config, 'transport', 'smtp');
        $this->assertPropertyEquals($config, 'host', 'mail.example.com');
        $this->assertPropertyEquals($config, 'port', 465);
        $this->assertPropertyEquals($config, 'auth', 'plain');
        $this->assertPropertyEquals($config, 'username', 'admin@example.com');
        $this->assertPropertyEquals($config, 'password', 'EmailPass123');
    }

    public function test_it_constructs_with_defaults(): void
    {
        $config = new EmailConfiguration(configure: false);

        $this->assertPropertyEquals($config, 'configure', false);
        $this->assertPropertyEquals($config, 'transport', 'sendmail');
        $this->assertPropertyEquals($config, 'host', '');
        $this->assertPropertyEquals($config, 'port', 587);
        $this->assertPropertyEquals($config, 'auth', '');
        $this->assertPropertyEquals($config, 'username', '');
        $this->assertPropertyEquals($config, 'password', '');
    }

    public function test_is_smtp_returns_true_for_smtp_transport(): void
    {
        $config = new EmailConfiguration(
            configure: true,
            transport: 'smtp'
        );

        $this->assertTrue($config->isSmtp());
    }

    public function test_is_smtp_returns_false_for_sendmail_transport(): void
    {
        $config = new EmailConfiguration(
            configure: true,
            transport: 'sendmail'
        );

        $this->assertFalse($config->isSmtp());
    }

    public function test_to_array_excludes_password_by_default(): void
    {
        $config = $this->createValidInstance();
        $array = $config->toArray();

        $this->assertArrayHasKey('configure', $array);
        $this->assertArrayHasKey('transport', $array);
        $this->assertArrayHasKey('host', $array);
        $this->assertArrayHasKey('port', $array);
        $this->assertArrayHasKey('auth', $array);
        $this->assertArrayHasKey('username', $array);
        $this->assertArrayNotHasKey('password', $array);
    }

    public function test_from_array_with_complete_data(): void
    {
        $data = [
            'configure' => true,
            'transport' => 'smtp',
            'host' => 'smtp.test',
            'port' => 25,
            'auth' => 'login',
            'username' => 'test@test.com',
            'password' => 'TestPass'
        ];

        $config = EmailConfiguration::fromArray($data);

        $this->assertPropertyEquals($config, 'configure', true);
        $this->assertPropertyEquals($config, 'transport', 'smtp');
        $this->assertPropertyEquals($config, 'host', 'smtp.test');
        $this->assertPropertyEquals($config, 'port', 25);
        $this->assertPropertyEquals($config, 'auth', 'login');
        $this->assertPropertyEquals($config, 'username', 'test@test.com');
        $this->assertPropertyEquals($config, 'password', 'TestPass');
    }

    public function test_from_array_with_missing_fields_uses_defaults(): void
    {
        $data = ['configure' => true];

        $config = EmailConfiguration::fromArray($data);

        $this->assertPropertyEquals($config, 'configure', true);
        $this->assertPropertyEquals($config, 'transport', 'sendmail');
        $this->assertPropertyEquals($config, 'host', '');
        $this->assertPropertyEquals($config, 'port', 587);
    }

    public function test_from_array_coerces_port_to_int(): void
    {
        $data = [
            'configure' => true,
            'port' => '465' // string
        ];

        $config = EmailConfiguration::fromArray($data);

        $this->assertPropertyEquals($config, 'port', 465);
        $this->assertIsInt($config->port);
    }

    public function test_supports_various_transports(): void
    {
        $transports = ['smtp', 'sendmail'];

        foreach ($transports as $transport) {
            $config = new EmailConfiguration(
                configure: true,
                transport: $transport
            );

            $this->assertPropertyEquals($config, 'transport', $transport);
        }
    }

    public function test_supports_various_smtp_ports(): void
    {
        $ports = [25, 465, 587, 2525];

        foreach ($ports as $port) {
            $config = new EmailConfiguration(
                configure: true,
                transport: 'smtp',
                host: 'smtp.test',
                port: $port
            );

            $this->assertPropertyEquals($config, 'port', $port);
        }
    }
}
