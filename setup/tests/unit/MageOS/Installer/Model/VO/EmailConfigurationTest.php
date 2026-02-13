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

    public function testItConstructsWithAllParameters(): void
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

    public function testItConstructsWithDefaults(): void
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

    public function testIsSmtpReturnsTrueForSmtpTransport(): void
    {
        $config = new EmailConfiguration(
            configure: true,
            transport: 'smtp'
        );

        $this->assertTrue($config->isSmtp());
    }

    public function testIsSmtpReturnsFalseForSendmailTransport(): void
    {
        $config = new EmailConfiguration(
            configure: true,
            transport: 'sendmail'
        );

        $this->assertFalse($config->isSmtp());
    }

    public function testToArrayExcludesPasswordByDefault(): void
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

    public function testFromArrayWithCompleteData(): void
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

    public function testFromArrayWithMissingFieldsUsesDefaults(): void
    {
        $data = ['configure' => true];

        $config = EmailConfiguration::fromArray($data);

        $this->assertPropertyEquals($config, 'configure', true);
        $this->assertPropertyEquals($config, 'transport', 'sendmail');
        $this->assertPropertyEquals($config, 'host', '');
        $this->assertPropertyEquals($config, 'port', 587);
    }

    public function testFromArrayCoercesPortToInt(): void
    {
        $data = [
            'configure' => true,
            'port' => '465' // string
        ];

        $config = EmailConfiguration::fromArray($data);

        $this->assertPropertyEquals($config, 'port', 465);
        $this->assertIsInt($config->port);
    }

    public function testSupportsVariousTransports(): void
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

    public function testSupportsVariousSmtpPorts(): void
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
