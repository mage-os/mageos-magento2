<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Vendor\LazyTypesFixture;

use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyTypes\CoreParentWithHook;

/**
 * The trusted core constructor calls _construct(), which runs third-party code here
 */
class ExtendsCoreOverridingHook extends CoreParentWithHook
{
    protected function _construct(): void
    {
        define('LAZY_TYPES_FIXTURE_HOOK_CONSTANT', true);
    }
}
