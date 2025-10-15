<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

use Magento\Quote\Model\Quote\Address\Total;

/**
 * Test helper for Quote Address Total used in unit tests.
 *
 * Provides explicit accessors that may be referenced by tests and
 * avoids the need to construct framework dependencies.
 */
class TotalTestHelper extends Total
{
    /**
     * Constructor intentionally empty to skip parent dependencies.
     */
    public function __construct()
    {
        // Intentionally skip parent constructor to avoid ObjectManager usage
    }

    /** @var float */
    private $grandTotal = 0.0;

    /** @var float */
    private $baseGrandTotal = 0.0;

    /**
     * Set grand total value for tests.
     *
     * @param float|int $grandTotal
     * @return $this
     */
    public function setGrandTotal($grandTotal)
    {
        $this->grandTotal = $grandTotal;
        return $this;
    }

    /**
     * Set base grand total value for tests.
     *
     * @param float|int $baseGrandTotal
     * @return $this
     */
    public function setBaseGrandTotal($baseGrandTotal)
    {
        $this->baseGrandTotal = $baseGrandTotal;
        return $this;
    }

    /**
     * Get grand total value.
     *
     * @return float|int
     */
    public function getGrandTotal()
    {
        return $this->grandTotal;
    }

    /**
     * Get base grand total value.
     *
     * @return float|int
     */
    public function getBaseGrandTotal()
    {
        return $this->baseGrandTotal;
    }
    
    /**
     * Explicit getter for total code used by tests.
     *
     * @return string|null
     */
    public function getCode()
    {
        return $this->getData('code');
    }
}
