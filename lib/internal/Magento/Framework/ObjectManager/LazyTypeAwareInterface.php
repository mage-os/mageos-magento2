<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\ObjectManager;

/**
 * Implemented by ObjectManager configs that carry a compile-time deny-list of types
 * that must not be lazy-loaded on PHP 8.4. Kept separate from ConfigInterface to avoid
 * forcing the method onto third-party config implementations.
 */
interface LazyTypeAwareInterface
{
    /**
     * Whether the given concrete type was flagged at compile-time as incompatible with
     * PHP 8.4 lazy ghosts. Implementations should return true (= non-lazy) when the
     * deny-list is empty so a stale cache cannot accidentally lazify unscanned types.
     */
    public function isNonLazyType(string $type): bool;
}
