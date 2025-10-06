<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

use Magento\Quote\Model\Quote\Address\Total;

/**
 * Helper class for providing Total getters in unit tests.
 */
class TotalTestHelper extends Total
{
    /** @var float */
    private float $subtotal = 0.0;

    /** @var float */
    private float $subtotalInclTax = 0.0;

    /** @var float */
    private float $grandTotal = 0.0;

    /** @var float */
    private float $discountTaxCompensationAmount = 0.0;

    /** @var float */
    private float $discountAmount = 0.0;

    /** @var string|null */
    private ?string $discountDescription = null;

    /** @var array */
    private array $appliedTaxes = [];

    public function __construct(
        float $subtotal = 0.0,
        float $subtotalInclTax = 0.0,
        float $grandTotal = 0.0
    ) {
        // Skip parent constructor
        $this->subtotal = $subtotal;
        $this->subtotalInclTax = $subtotalInclTax;
        $this->grandTotal = $grandTotal;
    }

    public function setDiscountDescription(?string $description): void
    {
        $this->discountDescription = $description;
    }

    public function setAppliedTaxes(array $appliedTaxes): void
    {
        $this->appliedTaxes = $appliedTaxes;
    }

    public function setDiscountAmount(float $amount): void
    {
        $this->discountAmount = $amount;
    }

    public function setDiscountTaxCompensationAmount(float $amount): void
    {
        $this->discountTaxCompensationAmount = $amount;
    }

    public function getSubtotal()
    {
        return $this->subtotal;
    }

    public function getSubtotalInclTax()
    {
        return $this->subtotalInclTax;
    }

    public function getGrandTotal()
    {
        return $this->grandTotal;
    }

    public function getDiscountTaxCompensationAmount()
    {
        return $this->discountTaxCompensationAmount;
    }

    public function getDiscountAmount()
    {
        return $this->discountAmount;
    }

    public function getDiscountDescription()
    {
        return $this->discountDescription;
    }

    public function getAppliedTaxes()
    {
        return $this->appliedTaxes;
    }
}





