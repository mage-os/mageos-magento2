<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyProxies;

use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyTypes\TraitUsingNonLazyTrait;

/**
 * Stateful subject using a trait that itself uses a #[NonLazy] trait.
 */
class NonLazyTraitSubject
{
    use TraitUsingNonLazyTrait;

    /**
     * @var string
     */
    private string $value = 'state';
}
