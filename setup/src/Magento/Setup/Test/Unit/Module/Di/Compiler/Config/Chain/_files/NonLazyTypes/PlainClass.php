<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\NonLazyTypes;

class PlainClass
{
    public function __construct(private string $value = '')
    {
    }
}
