<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyProxies;

/**
 * Static properties cannot trigger native lazy initialization.
 */
class StaticOnlySubject
{
    /**
     * @var string
     */
    public static string $value = '';
}
