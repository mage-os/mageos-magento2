<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Cache\Frontend\Adapter\SymfonyAdapters;

/**
 * Opt-in extension of TagAdapterInterface for adapters that can garbage-collect
 * tag bookkeeping orphaned by passive (TTL) expiry.
 *
 * Callers instanceof-check this interface instead of probing with method_exists().
 */
interface GarbageCollectingTagAdapterInterface extends TagAdapterInterface
{
    /**
     * Reap tag bookkeeping whose data keys have passively expired
     *
     * @param int|null $batchSize Maximum ids to inspect per call, or null for time-bounded only
     * @param float $maxRuntime Wall-time budget in seconds
     * @return int Number of orphaned set members cleaned
     */
    public function garbageCollect(?int $batchSize = null, float $maxRuntime = 2.0): int;
}
