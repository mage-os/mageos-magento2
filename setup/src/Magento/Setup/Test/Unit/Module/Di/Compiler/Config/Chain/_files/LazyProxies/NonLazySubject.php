<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyProxies;

use Magento\Framework\ObjectManager\Attribute\NonLazy;

/**
 * Stateful subject whose constructor side effects must not be skipped.
 */
#[NonLazy]
class NonLazySubject
{
    /**
     * @var string
     */
    private string $value = 'state';
}
