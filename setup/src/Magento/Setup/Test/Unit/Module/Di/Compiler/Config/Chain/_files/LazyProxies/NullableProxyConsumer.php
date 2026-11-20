<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyProxies;

/**
 * Consumer accepting a generated proxy or null.
 */
class NullableProxyConsumer
{
    /**
     * Preserve the generated proxy requirement within a nullable type.
     */
    public function __construct(public ?Subject\Proxy $dependency = null)
    {
    }
}
