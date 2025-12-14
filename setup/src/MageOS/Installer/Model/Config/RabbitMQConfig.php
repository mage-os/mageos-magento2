<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Config;

use MageOS\Installer\Model\Detector\RabbitMQDetector;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\note;
use function Laravel\Prompts\password;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\text;
use function Laravel\Prompts\warning;

/**
 * Collects RabbitMQ configuration with Laravel Prompts
 */
class RabbitMQConfig
{
    public function __construct(
        private readonly RabbitMQDetector $rabbitMQDetector
    ) {
    }

    /**
     * Collect RabbitMQ configuration
     *
     * @return array{enabled: bool, host: string, port: int, user: string, password: string, virtualhost: string}|null
     */
    public function collect(): ?array
    {
        note('RabbitMQ Configuration');

        // Detect RabbitMQ
        $detected = spin(
            message: 'Detecting RabbitMQ...',
            callback: fn () => $this->rabbitMQDetector->detect()
        );

        if (!$detected) {
            warning('RabbitMQ not detected on localhost:5672');

            $configureManually = confirm(
                label: 'Configure RabbitMQ manually?',
                default: false,
                hint: 'RabbitMQ is optional for async operations'
            );

            if (!$configureManually) {
                info('Skipping RabbitMQ configuration');
                return null;
            }

            return $this->collectManualConfig(null);
        }

        info(sprintf('✓ Detected RabbitMQ on %s:%d', $detected['host'], $detected['port']));

        $useDetected = confirm(
            label: 'Use detected RabbitMQ with default credentials (guest/guest)?',
            default: true,
            hint: 'Quick setup with standard credentials'
        );

        if ($useDetected) {
            info('✓ Using RabbitMQ with default credentials');
            return [
                'enabled' => true,
                'host' => $detected['host'],
                'port' => $detected['port'],
                'user' => 'guest',
                'password' => 'guest',
                'virtualhost' => '/'
            ];
        }

        info('Configure manually:');
        return $this->collectManualConfig($detected);
    }

    /**
     * Collect RabbitMQ configuration manually
     *
     * @param array{host: string, port: int}|null $detected
     * @return array{enabled: bool, host: string, port: int, user: string, password: string, virtualhost: string}
     */
    private function collectManualConfig(?array $detected): array
    {
        $defaultHost = $detected['host'] ?? 'localhost';
        $defaultPort = $detected['port'] ?? 5672;

        $host = text(
            label: 'RabbitMQ host',
            default: $defaultHost,
            placeholder: 'localhost',
            validate: fn ($value) => empty($value) ? 'Host cannot be empty' : null
        );

        $port = (int)text(
            label: 'RabbitMQ port',
            default: (string)$defaultPort,
            placeholder: '5672',
            validate: fn ($value) => !is_numeric($value) || $value < 1 || $value > 65535
                ? 'Port must be a number between 1 and 65535'
                : null
        );

        $user = text(
            label: 'RabbitMQ username',
            default: 'guest',
            placeholder: 'guest'
        );

        $pass = password(
            label: 'RabbitMQ password',
            hint: 'Default is usually "guest"',
            validate: fn ($value) => empty($value) ? 'Password cannot be empty' : null
        );

        $virtualhost = text(
            label: 'RabbitMQ virtual host',
            default: '/',
            placeholder: '/',
            hint: 'Usually "/" for default'
        );

        return [
            'enabled' => true,
            'host' => $host,
            'port' => $port,
            'user' => $user,
            'password' => $pass ?? 'guest',
            'virtualhost' => $virtualhost
        ];
    }
}
