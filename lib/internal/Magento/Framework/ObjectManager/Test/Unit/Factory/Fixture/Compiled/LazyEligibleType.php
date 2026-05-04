<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\ObjectManager\Test\Unit\Factory\Fixture\Compiled;

/**
 * Lazy-eligible fixture: declares a constructor (so the lazy-ghost gate accepts it) and
 * tracks invocation so tests can assert eager vs deferred construction.
 */
class LazyEligibleType
{
    public bool $constructorCalled = false;

    public function __construct()
    {
        $this->constructorCalled = true;
    }

    public function ping(): string
    {
        return 'pong';
    }
}
