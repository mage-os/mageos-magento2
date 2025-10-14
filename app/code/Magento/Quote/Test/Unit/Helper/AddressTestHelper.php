<?php
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

use Magento\Quote\Model\Quote\Address;

/**
 * Test helper for Address to support tax and shipping amount operations
 */
class AddressTestHelper extends Address
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * Skip parent constructor to avoid dependency injection issues
     */
    public function __construct()
    {
        // Skip parent constructor
    }

    /**
     * Get base tax amount
     *
     * @return float
     */
    public function getBaseTaxAmount()
    {
        return $this->data['baseTaxAmount'] ?? 0;
    }

    /**
     * Set base tax amount
     *
     * @param float $value
     * @return $this
     */
    public function setBaseTaxAmount($value)
    {
        $this->data['baseTaxAmount'] = $value;
        return $this;
    }

    /**
     * Get tax amount
     *
     * @return float
     */
    public function getTaxAmount()
    {
        return $this->data['taxAmount'] ?? 0;
    }

    /**
     * Set tax amount
     *
     * @param float $value
     * @return $this
     */
    public function setTaxAmount($value)
    {
        $this->data['taxAmount'] = $value;
        return $this;
    }

    /**
     * Get shipping tax amount
     *
     * @return float
     */
    public function getShippingTaxAmount()
    {
        return $this->data['shippingTaxAmount'] ?? 0;
    }

    /**
     * Set shipping tax amount
     *
     * @param float $value
     * @return $this
     */
    public function setShippingTaxAmount($value)
    {
        $this->data['shippingTaxAmount'] = $value;
        return $this;
    }

    /**
     * Get base shipping tax amount
     *
     * @return float
     */
    public function getBaseShippingTaxAmount()
    {
        return $this->data['baseShippingTaxAmount'] ?? 0;
    }

    /**
     * Set base shipping tax amount
     *
     * @param float $value
     * @return $this
     */
    public function setBaseShippingTaxAmount($value)
    {
        $this->data['baseShippingTaxAmount'] = $value;
        return $this;
    }

    /**
     * Get shipping amount
     *
     * @return float
     */
    public function getShippingAmount()
    {
        return $this->data['shippingAmount'] ?? 0;
    }

    /**
     * Set shipping amount
     *
     * @param float $value
     * @param bool $alreadyExclTax
     * @return $this
     */
    public function setShippingAmount($value, $alreadyExclTax = false)
    {
        $this->data['shippingAmount'] = $value;
        return $this;
    }

    /**
     * Get base shipping amount
     *
     * @return float
     */
    public function getBaseShippingAmount()
    {
        return $this->data['baseShippingAmount'] ?? 0;
    }

    /**
     * Set base shipping amount
     *
     * @param float $value
     * @param bool $alreadyExclTax
     * @return $this
     */
    public function setBaseShippingAmount($value, $alreadyExclTax = false)
    {
        $this->data['baseShippingAmount'] = $value;
        return $this;
    }
}

