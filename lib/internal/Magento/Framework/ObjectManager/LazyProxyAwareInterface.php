<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\ObjectManager;

/**
 * Exposes compile-time targets eligible for native lazy proxy construction.
 */
interface LazyProxyAwareInterface
{
    /**
     * Return the concrete real-instance class, or null when native construction was not proven safe.
     *
     * @param string $proxyType
     * @return string|null
     */
    public function getLazyProxyTarget(string $proxyType): ?string;
}
