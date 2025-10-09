<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Test\Unit\Helper;

use Magento\Framework\Pricing\Amount\Base;

/**
 * Test helper for Amount class
 */
class AmountTestHelper extends Base
{
    /**
     * @var array
     */
    protected $adjustmentAmounts;

    /**
     * @var float
     */
    protected $value;

    public function __construct()
    {
        // Skip parent constructor to avoid dependencies
    }

    public function __wakeup()
    {
        // Empty implementation for testing
    }

    /**
     * Get adjustment amounts (custom method for testing)
     *
     * @return mixed
     */
    public function getAdjustmentAmounts()
    {
        return $this->adjustmentAmounts;
    }

    /**
     * Set adjustment amounts (custom method for testing)
     *
     * @param mixed $adjustmentAmounts
     * @return $this
     */
    public function setAdjustmentAmounts($adjustmentAmounts): self
    {
        $this->adjustmentAmounts = $adjustmentAmounts;
        return $this;
    }

    /**
     * Get value (override parent method for testing)
     *
     * @param mixed $exclude
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getValue($exclude = null)
    {
        return $this->value;
    }

    /**
     * Set value (custom method for testing)
     *
     * @param mixed $value
     * @return $this
     */
    public function setValue($value): self
    {
        $this->value = $value;
        return $this;
    }
}
