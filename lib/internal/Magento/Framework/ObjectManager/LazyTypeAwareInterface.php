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
     * Whether the given concrete type must not be constructed as a PHP 8.4 lazy ghost.
     *
     * Only types proven lazy-eligible at compile time may return false; unknown types
     * are non-lazy. Fails safe: returns true (= non-lazy) for everything when no
     * compile-time data is present.
     *
     * @param string $type
     * @return bool
     */
    public function isNonLazyType(string $type): bool;
}
