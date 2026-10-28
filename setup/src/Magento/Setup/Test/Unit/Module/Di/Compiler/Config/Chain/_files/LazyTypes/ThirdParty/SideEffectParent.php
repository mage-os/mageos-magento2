<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Vendor\LazyTypesFixture;

class SideEffectParent
{
    /**
     * @var mixed
     */
    protected mixed $value;

    public function __construct(mixed $value)
    {
        $this->value = $value;
        $this->init();
    }

    protected function init(): void
    {
    }
}
