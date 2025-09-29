<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Weee\Test\Unit\Helper;

use Magento\Weee\Api\Data\ProductRender\WeeeAdjustmentAttributeInterface;

/**
 * Test helper for WeeeAdjustmentAttributeInterface
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class WeeeAdjustmentAttributeInterfaceTestHelper implements WeeeAdjustmentAttributeInterface
{
    /**
     * @var mixed
     */
    private $data = null;

    /**
     * @var int
     */
    private int $callCount = 0;

    /**
     * Constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get data
     *
     * @param mixed $key
     * @return mixed
     */
    public function getData($key = null)
    {
        if ($this->callCount == 0) {
            $this->callCount++;
            return [
                'amount' => 12.1,
                'tax_amount' => 12,
                'amount_excl_tax' => 71
            ];
        } elseif ($this->callCount == 1 && $key == 'amount') {
            $this->callCount++;
            return 12.1;
        } elseif ($this->callCount == 2 && $key == 'tax_amount') {
            $this->callCount++;
            return 12.1;
        } elseif ($this->callCount == 3 && $key == 'amount_excl_tax') {
            $this->callCount++;
            return 12.1;
        } elseif ($this->callCount == 4) {
            $this->callCount++;
            return 12.1;
        }
        return null;
    }

    /**
     * Set data
     *
     * @param mixed $value
     * @return $this
     */
    public function setData($value): self
    {
        $this->data = $value;
        return $this;
    }

    /**
     * Set amount
     *
     * @param mixed $amount
     * @return $this
     */
    public function setAmount($amount): self
    {
        return $this;
    }

    /**
     * Get amount
     *
     * @return mixed
     */
    public function getAmount()
    {
        return null;
    }

    /**
     * Get tax amount
     *
     * @return mixed
     */
    public function getTaxAmount()
    {
        return null;
    }

    /**
     * Set tax amount
     *
     * @param mixed $taxAmount
     * @return $this
     */
    public function setTaxAmount($taxAmount): self
    {
        return $this;
    }

    /**
     * Set amount excl tax
     *
     * @param mixed $amountExclTax
     * @return $this
     */
    public function setAmountExclTax($amountExclTax): self
    {
        return $this;
    }

    /**
     * Set tax amount incl tax
     *
     * @param mixed $taxAmountInclTax
     * @return $this
     */
    public function setTaxAmountInclTax($taxAmountInclTax): self
    {
        return $this;
    }

    /**
     * Get tax amount incl tax
     *
     * @return mixed
     */
    public function getTaxAmountInclTax()
    {
        return null;
    }

    /**
     * Get amount excl tax
     *
     * @return mixed
     */
    public function getAmountExclTax()
    {
        return null;
    }

    /**
     * Set attribute code
     *
     * @param mixed $attributeCode
     * @return $this
     */
    public function setAttributeCode($attributeCode): self
    {
        return $this;
    }

    /**
     * Get attribute code
     *
     * @return mixed
     */
    public function getAttributeCode()
    {
        return null;
    }

    /**
     * Get extension attributes
     *
     * @return mixed
     */
    public function getExtensionAttributes()
    {
        return null;
    }

    /**
     * Set extension attributes
     *
     * @param mixed $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes($extensionAttributes): self
    {
        return $this;
    }
}
