<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Vendor\LazyTypesFixture;

class ConditionalConstructor
{
    /**
     * @var mixed
     */
    private mixed $value = null;

    public function __construct(mixed $value = null)
    {
        if ($value !== null) {
            $this->value = $value;
        }
    }
}
