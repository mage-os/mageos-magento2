<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Detector;

/**
 * Detects Redis availability
 */
class RedisDetector
{
    /**
     * @var array<array{host: string, port: int, name: string}>
     */
    private array $commonInstances = [
        ['host' => '127.0.0.1', 'port' => 6379, 'name' => 'default'],
        ['host' => 'localhost', 'port' => 6379, 'name' => 'default'],
        ['host' => 'redis', 'port' => 6379, 'name' => 'docker'],
        ['host' => '127.0.0.1', 'port' => 6380, 'name' => 'secondary'],
        ['host' => '127.0.0.1', 'port' => 6381, 'name' => 'tertiary'],
    ];

    /**
     * Detect available Redis instances
     *
     * @return array<array{host: string, port: int, name: string}>
     */
    public function detect(): array
    {
        $available = [];

        foreach ($this->commonInstances as $instance) {
            if ($this->isRedisAvailable($instance['host'], $instance['port'])) {
                $available[] = $instance;
            }
        }

        return $available;
    }

    /**
     * Check if Redis is available at host:port
     *
     * @param string $host
     * @param int $port
     * @return bool
     */
    private function isRedisAvailable(string $host, int $port): bool
    {
        $connection = @fsockopen($host, $port, $errno, $errstr, 2);

        if (!$connection) {
            return false;
        }

        // Try to send PING command
        fwrite($connection, "PING\r\n");
        $response = fgets($connection);
        fclose($connection);

        return str_contains((string)$response, 'PONG') || str_contains((string)$response, '+');
    }
}
