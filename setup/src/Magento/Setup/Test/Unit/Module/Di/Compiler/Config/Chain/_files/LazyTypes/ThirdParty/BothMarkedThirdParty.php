<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Vendor\LazyTypesFixture;

use Magento\Framework\ObjectManager\Attribute\Lazy;
use Magento\Framework\ObjectManager\Attribute\NonLazy;

#[Lazy]
#[NonLazy]
class BothMarkedThirdParty
{
    public function __construct()
    {
    }
}
