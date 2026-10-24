<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Vendor\LazyTypesFixture;

use Magento\Framework\ObjectManager\Attribute\Lazy;

#[Lazy]
final class OptedInFinalThirdParty
{
    public function __construct()
    {
    }
}
