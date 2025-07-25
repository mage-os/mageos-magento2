<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Csp\Model;

use Magento\Framework\App\ObjectManager;
use Psr\Log\LoggerInterface;

/**
 * Collector of Integrity objects.
 * 
 * Uses static storage to persist data across ObjectManager instances
 * during area emulation in static content deployment.
 */
class SubresourceIntegrityCollector
{
    /**
     * Global storage that persists across ObjectManager instances
     * @var array
     */
    private static array $globalData = [];

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param LoggerInterface|null $logger
     */
    public function __construct(?LoggerInterface $logger = null) {
        $this->logger = $logger ?? ObjectManager::getInstance()->get(LoggerInterface::class);
        
        $this->logger->info('SRI Collector: Initialized with ' . count(self::$globalData) . ' objects (global storage)');
    }

    /**
     * Collects given Integrity object.
     *
     * @param SubresourceIntegrity $integrity
     *
     * @return void
     */
    public function collect(SubresourceIntegrity $integrity): void
    {
        self::$globalData[] = $integrity;
        $this->logger->info('SRI Collector: Collected object, total: ' . count(self::$globalData));
    }

    /**
     * Provides all collected Integrity objects.
     *
     * @return SubresourceIntegrity[]
     */
    public function release(): array
    {
        $count = count(self::$globalData);
        $this->logger->info('SRI Collector: Releasing ' . $count . ' objects');
        return self::$globalData;
    }

    /**
     * Clear all collected data.
     *
     * @return void
     */
    public function clear(): void
    {
        $count = count(self::$globalData);
        self::$globalData = [];
        $this->logger->info('SRI Collector: Cleared ' . $count . ' objects');
    }
}
