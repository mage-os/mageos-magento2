<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Checkout\Test\Unit\Helper;

use Magento\Quote\Api\Data\TotalsInterface;

/**
 * Minimal TotalsInterface implementation for unit tests with full
 * PHPDoc and multi-line methods to satisfy static checks and styling.
 */
class TotalsInterfaceTestHelper implements TotalsInterface
{
    /** @var array<int, mixed> */
    private $items = [];

    /** @var array<int, mixed> */
    private $segments = [];

    public function getGrandTotal()
    {
        return null;
    }

    public function setGrandTotal($grandTotal)
    {
        return $this;
    }

    public function getBaseGrandTotal()
    {
        return null;
    }

    public function setBaseGrandTotal($baseGrandTotal)
    {
        return $this;
    }

    public function getSubtotal()
    {
        return null;
    }

    public function setSubtotal($subtotal)
    {
        return $this;
    }

    public function getBaseSubtotal()
    {
        return null;
    }

    public function setBaseSubtotal($baseSubtotal)
    {
        return $this;
    }

    public function getDiscountAmount()
    {
        return null;
    }

    public function setDiscountAmount($discountAmount)
    {
        return $this;
    }

    public function getBaseDiscountAmount()
    {
        return null;
    }

    public function setBaseDiscountAmount($baseDiscountAmount)
    {
        return $this;
    }

    public function getSubtotalWithDiscount()
    {
        return null;
    }

    public function setSubtotalWithDiscount($subtotalWithDiscount)
    {
        return $this;
    }

    public function getBaseSubtotalWithDiscount()
    {
        return null;
    }

    public function setBaseSubtotalWithDiscount($baseSubtotalWithDiscount)
    {
        return $this;
    }

    public function getShippingAmount()
    {
        return null;
    }

    public function setShippingAmount($shippingAmount)
    {
        return $this;
    }

    public function getBaseShippingAmount()
    {
        return null;
    }

    public function setBaseShippingAmount($baseShippingAmount)
    {
        return $this;
    }

    public function getShippingDiscountAmount()
    {
        return null;
    }

    public function setShippingDiscountAmount($shippingDiscountAmount)
    {
        return $this;
    }

    public function getBaseShippingDiscountAmount()
    {
        return null;
    }

    public function setBaseShippingDiscountAmount($baseShippingDiscountAmount)
    {
        return $this;
    }

    public function getTaxAmount()
    {
        return null;
    }

    public function setTaxAmount($taxAmount)
    {
        return $this;
    }

    public function getBaseTaxAmount()
    {
        return null;
    }

    public function setBaseTaxAmount($baseTaxAmount)
    {
        return $this;
    }

    public function getWeeeTaxAppliedAmount()
    {
        return null;
    }

    public function setWeeeTaxAppliedAmount($weeeTaxAppliedAmount)
    {
        return $this;
    }

    public function getShippingTaxAmount()
    {
        return null;
    }

    public function setShippingTaxAmount($shippingTaxAmount)
    {
        return $this;
    }

    public function getBaseShippingTaxAmount()
    {
        return null;
    }

    public function setBaseShippingTaxAmount($baseShippingTaxAmount)
    {
        return $this;
    }

    public function getSubtotalInclTax()
    {
        return null;
    }

    public function setSubtotalInclTax($subtotalInclTax)
    {
        return $this;
    }

    public function getBaseSubtotalInclTax()
    {
        return null;
    }

    public function setBaseSubtotalInclTax($baseSubtotalInclTax)
    {
        return $this;
    }

    public function getShippingInclTax()
    {
        return null;
    }

    public function setShippingInclTax($shippingInclTax)
    {
        return $this;
    }

    public function getBaseShippingInclTax()
    {
        return null;
    }

    public function setBaseShippingInclTax($baseShippingInclTax)
    {
        return $this;
    }

    public function getBaseCurrencyCode()
    {
        return null;
    }

    public function setBaseCurrencyCode($baseCurrencyCode)
    {
        return $this;
    }

    public function getQuoteCurrencyCode()
    {
        return null;
    }

    public function setQuoteCurrencyCode($quoteCurrencyCode)
    {
        return $this;
    }

    public function getCouponCode()
    {
        return null;
    }

    public function setCouponCode($couponCode)
    {
        return $this;
    }

    public function getItemsQty()
    {
        return null;
    }

    public function setItemsQty($itemsQty = null)
    {
        return $this;
    }

    public function getItems()
    {
        return $this->items;
    }

    public function setItems(?array $items = null)
    {
        $this->items = $items ?? [];
        return $this;
    }

    public function getTotalSegments()
    {
        return $this->segments;
    }

    public function setTotalSegments($totals = [])
    {
        $this->segments = $totals;
        return $this;
    }

    public function getExtensionAttributes()
    {
        return null;
    }

    public function setExtensionAttributes(\Magento\Quote\Api\Data\TotalsExtensionInterface $extensionAttributes)
    {
        return $this;
    }

    public function toArray()
    {
        return ['items' => $this->items, 'total_segments' => $this->segments];
    }
}



