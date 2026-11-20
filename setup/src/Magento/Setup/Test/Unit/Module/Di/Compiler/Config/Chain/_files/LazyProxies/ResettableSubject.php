<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyProxies;

use Magento\Framework\ObjectManager\ResetAfterRequestInterface;

/**
 * Subject whose reset hook must not trigger lazy initialization.
 */
class ResettableSubject extends Subject implements ResetAfterRequestInterface
{
    /**
     * @var int
     */
    public int $resets = 0;

    /**
     * Record reset invocation.
     */
    public function _resetState(): void
    {
        $this->resets++;
    }
}
