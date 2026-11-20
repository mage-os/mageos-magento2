<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\ObjectManager\Test\Unit\Factory\Fixture\Compiled;

/**
 * Stateful subject whose method triggers native lazy initialization.
 */
class NativeProxySubject
{
    /**
     * Store the result returned after initialization.
     */
    public function __construct(private string $value = 'subject result')
    {
    }

    /**
     * Read instance state so lazy proxies must resolve their real instance.
     */
    public function ping(): string
    {
        return $this->value;
    }

    /**
     * Change state to verify that a native proxy observes the shared real instance.
     */
    public function setValue(string $value): void
    {
        $this->value = $value;
    }
}
