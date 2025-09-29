<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Test\Unit\Helper;

use Magento\Quote\Model\Quote\Address\Total;

/**
 * Test helper for Quote Address Total
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class QuoteAddressTotalTestHelper extends Total
{
    /**
     * @var array
     */
    private $weeeCodeToItemMap = [];

    /**
     * @var array
     */
    private $extraTaxableDetails = [];

    /**
     * @var float
     */
    private $weeeTotalExclTax = 0;

    /**
     * @var float
     */
    private $weeeBaseTotalExclTax = 0;

    /**
     * Constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get weee code to item map
     *
     * @return array
     */
    public function getWeeeCodeToItemMap()
    {
        return $this->weeeCodeToItemMap;
    }

    /**
     * Set weee code to item map
     *
     * @param array $map
     * @return $this
     */
    public function setWeeeCodeToItemMap($map)
    {
        $this->weeeCodeToItemMap = $map;
        return $this;
    }

    /**
     * Get extra taxable details
     *
     * @return array
     */
    public function getExtraTaxableDetails()
    {
        return $this->extraTaxableDetails;
    }

    /**
     * Set extra taxable details
     *
     * @param array $details
     * @return $this
     */
    public function setExtraTaxableDetails($details)
    {
        $this->extraTaxableDetails = $details;
        return $this;
    }

    /**
     * Get weee total excl tax
     *
     * @return float
     */
    public function getWeeeTotalExclTax()
    {
        return $this->weeeTotalExclTax;
    }

    /**
     * Set weee total excl tax
     *
     * @param float $total
     * @return $this
     */
    public function setWeeeTotalExclTax($total)
    {
        $this->weeeTotalExclTax = $total;
        return $this;
    }

    /**
     * Get weee base total excl tax
     *
     * @return float
     */
    public function getWeeeBaseTotalExclTax()
    {
        return $this->weeeBaseTotalExclTax;
    }

    /**
     * Set weee base total excl tax
     *
     * @param float $total
     * @return $this
     */
    public function setWeeeBaseTotalExclTax($total)
    {
        $this->weeeBaseTotalExclTax = $total;
        return $this;
    }
}
