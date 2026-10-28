<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Vendor\LazyTypesFixture;

class ChildOfTrivialParent extends TrivialAssigns
{
    /**
     * @var mixed
     */
    private mixed $extra;

    public function __construct(mixed $config, mixed $extra = null)
    {
        parent::__construct($config, limit: 10);
        $this->extra = $extra;
    }
}
