<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Detector;

/**
 * Detects RabbitMQ availability
 */
class RabbitMQDetector
{
    /**
     * @var array<array{host: string, port: int}>
     */
    private array $commonHosts = [
        ['host' => 'localhost', 'port' => 5672],
        ['host' => '127.0.0.1', 'port' => 5672],
        ['host' => 'rabbitmq', 'port' => 5672],
    ];

    /**
     * Detect if RabbitMQ is running
     *
     * @return array{host: string, port: int}|null
     */
    public function detect(): ?array
    {
        foreach ($this->commonHosts as $hostConfig) {
            if ($this->isPortOpen($hostConfig['host'], $hostConfig['port'])) {
                return $hostConfig;
            }
        }

        return null;
    }

    /**
     * Check if a port is open
     *
     * @param string $host
     * @param int $port
     * @param int $timeout
     * @return bool
     */
    private function isPortOpen(string $host, int $port, int $timeout = 2): bool
    {
        $connection = @fsockopen($host, $port, $errno, $errstr, $timeout);

        if ($connection) {
            fclose($connection);
            return true;
        }

        return false;
    }
}
