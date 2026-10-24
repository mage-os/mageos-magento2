<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Vendor\LazyTypesFixture;

trait TrivialConstructorTrait
{
    /**
     * @var mixed
     */
    private mixed $value;

    public function __construct(mixed $value = null)
    {
        $this->value = $value;
    }
}
