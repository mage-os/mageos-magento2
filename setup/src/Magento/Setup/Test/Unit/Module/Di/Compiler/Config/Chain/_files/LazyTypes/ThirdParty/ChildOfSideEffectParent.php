<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Vendor\LazyTypesFixture;

class ChildOfSideEffectParent extends SideEffectParent
{
    /**
     * @var mixed
     */
    private mixed $extra;

    public function __construct(mixed $value, mixed $extra = null)
    {
        $this->extra = $extra;
        parent::__construct($value);
    }
}
