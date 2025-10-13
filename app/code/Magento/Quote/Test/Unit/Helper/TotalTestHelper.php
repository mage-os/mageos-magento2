<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

use Magento\Quote\Model\Quote\Address\Total;

class TotalTestHelper extends Total
{
    public function __construct()
    {
        // Intentionally skip parent constructor to avoid ObjectManager usage
    }

    /** @var float */
    private $grandTotal = 0.0;

    /** @var float */
    private $baseGrandTotal = 0.0;

    public function setGrandTotal($grandTotal)
    {
        $this->grandTotal = $grandTotal;
        return $this;
    }

    public function setBaseGrandTotal($baseGrandTotal)
    {
        $this->baseGrandTotal = $baseGrandTotal;
        return $this;
    }

    public function getGrandTotal()
    {
        return $this->grandTotal;
    }

    public function getBaseGrandTotal()
    {
        return $this->baseGrandTotal;
    }
}
