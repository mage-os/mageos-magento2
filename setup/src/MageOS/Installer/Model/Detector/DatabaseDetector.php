<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Detector;

/**
 * Detects database availability
 */
class DatabaseDetector
{
    /**
     * @var array<int>
     */
    private array $commonPorts = [3306, 3307];

    /**
     * Detect if MySQL/MariaDB is running on localhost
     *
     * @return array{host: string, port: int}|null
     */
    public function detect(): ?array
    {
        foreach ($this->commonPorts as $port) {
            if ($this->isPortOpen('127.0.0.1', $port)) {
                return [
                    'host' => 'localhost',
                    'port' => $port
                ];
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
        // phpcs:ignore Magento2.Functions.DiscouragedFunction
        $connection = @fsockopen($host, $port, $errno, $errstr, $timeout);

        if ($connection) {
            fclose($connection);
            return true;
        }

        return false;
    }
}
