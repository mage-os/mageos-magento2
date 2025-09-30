<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Test\Unit\Helper;

use Magento\Framework\Pricing\Amount\AmountInterface;

/**
 * Test helper class for AmountInterface with custom methods
 *
 * This helper implements AmountInterface and adds custom methods
 * that don't exist on the interface for testing purposes.
 */
class AmountInterfaceTestHelper implements AmountInterface
{
    private $data = [];

    /**
     * Get value
     *
     * @param mixed $exclude
     * @return float
     */
    public function getValue($exclude = null): float
    {
        return $this->data['value'] ?? 0.0;
    }

    /**
     * Set value for testing
     *
     * @param float $value
     * @return self
     */
    public function setValue(float $value): self
    {
        $this->data['value'] = $value;
        return $this;
    }

    /**
     * Get base amount
     *
     * @return float
     */
    public function getBaseAmount(): float
    {
        return $this->data['base_amount'] ?? 0.0;
    }

    /**
     * Set base amount for testing
     *
     * @param float $baseAmount
     * @return self
     */
    public function setBaseAmount(float $baseAmount): self
    {
        $this->data['base_amount'] = $baseAmount;
        return $this;
    }

    /**
     * Required interface methods - implementing with default values
     */

    /**
     * Return string representation of amount value
     *
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->getValue();
    }

    /**
     * Return adjustment amount part value by adjustment code
     *
     * @param string $adjustmentCode
     * @return float
     */
    public function getAdjustmentAmount($adjustmentCode): float
    {
        return $this->data['adjustment_amounts'][$adjustmentCode] ?? 0.0;
    }

    /**
     * Return total adjustment amount value
     *
     * @return float
     */
    public function getTotalAdjustmentAmount(): float
    {
        return $this->data['total_adjustment_amount'] ?? 0.0;
    }

    /**
     * Return adjustment amounts
     *
     * @return array
     */
    public function getAdjustmentAmounts(): array
    {
        return $this->data['adjustment_amounts'] ?? [];
    }

    /**
     * Check if adjustment exists
     *
     * @param string $adjustmentCode
     * @return bool
     */
    public function hasAdjustment($adjustmentCode): bool
    {
        return isset($this->data['adjustment_amounts'][$adjustmentCode]);
    }
}
