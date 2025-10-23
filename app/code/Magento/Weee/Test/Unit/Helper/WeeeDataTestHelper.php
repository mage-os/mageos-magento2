<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Weee\Test\Unit\Helper;

class WeeeDataTestHelper
{
    /** @var array */
    private array $amounts = [];
    /** @var int */
    private int $callCount = 0;

    public function setAmounts(array $amounts): self
    {
        $this->amounts = $amounts;
        $this->callCount = 0;
        return $this;
    }

    public function getAmountExclTax()
    {
        return $this->amounts[$this->callCount++] ?? null;
    }
}
