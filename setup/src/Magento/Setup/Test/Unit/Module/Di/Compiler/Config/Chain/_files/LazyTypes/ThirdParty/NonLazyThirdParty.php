<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Vendor\LazyTypesFixture;

use Magento\Framework\ObjectManager\Attribute\NonLazy;

#[NonLazy]
class NonLazyThirdParty
{
    public function __construct()
    {
    }
}
