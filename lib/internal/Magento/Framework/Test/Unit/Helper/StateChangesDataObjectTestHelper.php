<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Test\Unit\Helper;

use Magento\Framework\DataObject;

/**
 * Test helper for state changes DataObject
 *
 * This helper extends DataObject to provide test-specific functionality
 * for tracking state changes (tax, price, discount).
 */
class StateChangesDataObjectTestHelper extends DataObject
{
    /**
     * @var bool
     */
    private $isTaxChanged;

    /**
     * @var bool
     */
    private $isPriceChanged;

    /**
     * @var bool
     */
    private $isDiscountChanged;

    /**
     * Constructor
     *
     * @param bool $isTaxChanged
     * @param bool $isPriceChanged
     * @param bool $isDiscountChanged
     */
    public function __construct($isTaxChanged, $isPriceChanged, $isDiscountChanged)
    {
        $this->isTaxChanged = $isTaxChanged;
        $this->isPriceChanged = $isPriceChanged;
        $this->isDiscountChanged = $isDiscountChanged;
        // Don't call parent constructor to avoid dependencies
    }

    /**
     * Check if tax changed
     *
     * @return bool
     */
    public function getIsTaxChanged()
    {
        return $this->isTaxChanged;
    }

    /**
     * Check if price changed
     *
     * @return bool
     */
    public function getIsPriceChanged()
    {
        return $this->isPriceChanged;
    }

    /**
     * Check if discount changed
     *
     * @return bool
     */
    public function getIsDiscountChanged()
    {
        return $this->isDiscountChanged;
    }
}

