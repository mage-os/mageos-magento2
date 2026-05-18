<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\NonLazyTypes;

use Magento\Framework\ObjectManager\Attribute\NonLazy;

#[NonLazy]
class MarkedNonLazy
{
    public function __construct()
    {
    }
}
