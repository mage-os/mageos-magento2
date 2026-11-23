<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyProxies;

/**
 * Consumer accepting a generated proxy or a scalar alternative.
 */
class UnionProxyConsumer
{
    /**
     * Preserve the generated proxy requirement within a union type.
     */
    public function __construct(public Subject\Proxy|string|null $dependency)
    {
    }
}
