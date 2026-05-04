<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\ObjectManager;

/**
 * Implemented by ObjectManager configs that track types that must not be lazy-loaded on PHP 8.4+.
 */
interface LazyTypeAwareInterface
{
    /**
     * Whether the given concrete type was flagged at compile-time as incompatible with PHP 8.4 lazy ghosts.
     *
     * Fails safe: Returns true (= non-lazy) if no compile-time data is present.
     *
     * @param string $type
     * @return bool
     */
    public function isNonLazyType(string $type): bool;
}
