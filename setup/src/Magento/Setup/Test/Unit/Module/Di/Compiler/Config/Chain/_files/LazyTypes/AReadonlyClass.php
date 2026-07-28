<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyTypes;

// phpcs:disable PHPCompatibility.Classes.NewReadonlyClasses
readonly class AReadonlyClass
{
    public function __construct(public string $value = '')
    {
    }
}
