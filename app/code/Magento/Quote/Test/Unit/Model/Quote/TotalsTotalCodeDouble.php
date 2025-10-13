<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */

declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Model\Quote;

use Magento\Quote\Model\Quote\Address\Total;

class TotalsTotalCodeDouble extends Total
{
    /**
     * @var string|null
     */
    private $code;

    public function __construct()
    {
        // Skip parent constructor
    }

    public function setCode(string $code)
    {
        $this->code = $code;
        return $this;
    }

    public function getCode()
    {
        return $this->code ?? (string)$this->getData('code');
    }
}
