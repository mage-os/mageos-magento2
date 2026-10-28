<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Vendor\LazyTypesFixture;

use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyTypes\CoreParentWithHook;

class ExtendsCoreOverridingInitSelect extends CoreParentWithHook
{
    protected function _initSelect(): void
    {
        define('LAZY_TYPES_FIXTURE_INIT_SELECT_CONSTANT', true);
    }
}
