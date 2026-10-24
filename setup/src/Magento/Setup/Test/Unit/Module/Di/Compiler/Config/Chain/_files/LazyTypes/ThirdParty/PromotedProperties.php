<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Vendor\LazyTypesFixture;

class PromotedProperties
{
    public function __construct(
        private readonly PlainThirdParty $dependency,
        private array $data = [],
        private string $type = PlainThirdParty::class
    ) {
    }
}
