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
 */
class SubresourceIntegrityCollector
{
    /**
     * @var array
     */
    private array $data = [];

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param LoggerInterface|null $logger
     */
    public function __construct(?LoggerInterface $logger = null) {
        $this->logger = $logger ?? ObjectManager::getInstance()->get(LoggerInterface::class);
        
        $this->logger->info('SRI Collector: Initialized (PID: ' . getmypid() . ')');
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
        $this->data[] = $integrity;
        $this->logger->info('SRI Collector: Collected "' . $integrity->getPath() . '" - Total: ' . count($this->data) . ' (PID: ' . getmypid() . ')');
    }

    /**
     * Provides all collected Integrity objects.
     *
     * @return SubresourceIntegrity[]
     */
    public function release(): array
    {
        $count = count($this->data);
        $this->logger->info('SRI Collector: Releasing ' . $count . ' objects (PID: ' . getmypid() . ')');
        return $this->data;
    }

    /**
     * Clear all collected data.
     *
     * @return void
     */
    public function clear(): void
    {
        $count = count($this->data);
        $this->data = [];
        $this->logger->info('SRI Collector: Cleared ' . $count . ' objects (PID: ' . getmypid() . ')');
    }
}
