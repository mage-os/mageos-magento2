<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyTypes;

/**
 * Core class delegating to a third-party constructor that is provably side-effect-free, so it stays lazy
 */
class CoreChainingToTrivialThirdParty extends \Vendor\LazyTypesFixture\TrivialAssigns
{
    /**
     * @var mixed
     */
    private mixed $extra;

    public function __construct(mixed $config, mixed $extra = null)
    {
        $this->extra = $extra;
        parent::__construct($config);
    }
}
