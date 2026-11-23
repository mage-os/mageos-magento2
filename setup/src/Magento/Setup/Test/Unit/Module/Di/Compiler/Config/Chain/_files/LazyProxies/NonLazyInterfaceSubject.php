<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyProxies;

use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyTypes\NonLazyInterface;

/**
 * Stateful subject implementing a #[NonLazy] interface.
 */
class NonLazyInterfaceSubject implements NonLazyInterface
{
    /**
     * @var string
     */
    private string $value = 'state';
}
