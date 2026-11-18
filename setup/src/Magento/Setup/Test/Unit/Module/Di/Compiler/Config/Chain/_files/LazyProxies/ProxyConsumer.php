<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyProxies;

/**
 * Consumer that requires the generated proxy subclass rather than its subject.
 */
class ProxyConsumer
{
    /**
     * Retain the explicit generated proxy type required by this consumer.
     */
    public function __construct(public Subject\Proxy $dependency)
    {
    }
}
