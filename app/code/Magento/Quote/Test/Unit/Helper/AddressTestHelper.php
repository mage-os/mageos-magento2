<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

use Magento\Quote\Model\Quote\Address;

/**
 * Test helper for Address
 *
 * This helper extends the concrete Address class to provide
 * test-specific functionality without dependency injection issues.
 */
class AddressTestHelper extends Address
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * Constructor that skips parent initialization
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    public function getBaseTaxAmount()
    {
        return $this->data['baseTaxAmount'] ?? 0;
    }

    public function setBaseTaxAmount($value)
    {
        $this->data['baseTaxAmount'] = $value;
        return $this;
    }

    public function getTaxAmount()
    {
        return $this->data['taxAmount'] ?? 0;
    }

    public function setTaxAmount($value)
    {
        $this->data['taxAmount'] = $value;
        return $this;
    }

    public function getShippingTaxAmount()
    {
        return $this->data['shippingTaxAmount'] ?? 0;
    }

    public function setShippingTaxAmount($value)
    {
        $this->data['shippingTaxAmount'] = $value;
        return $this;
    }

    public function getBaseShippingTaxAmount()
    {
        return $this->data['baseShippingTaxAmount'] ?? 0;
    }

    public function setBaseShippingTaxAmount($value)
    {
        $this->data['baseShippingTaxAmount'] = $value;
        return $this;
    }

    public function getShippingAmount()
    {
        return $this->data['shippingAmount'] ?? 0;
    }

    public function setShippingAmount($value, $alreadyExclTax = false)
    {
        $this->data['shippingAmount'] = $value;
        return $this;
    }

    public function getBaseShippingAmount()
    {
        return $this->data['baseShippingAmount'] ?? 0;
    }

    public function setBaseShippingAmount($value, $alreadyExclTax = false)
    {
        $this->data['baseShippingAmount'] = $value;
        return $this;
    }

    public function getShippingMethod()
    {
        return $this->data['shippingMethod'] ?? null;
    }

    public function setShippingMethod($method)
    {
        $this->data['shippingMethod'] = $method;
        return $this;
    }

    public function setShippingDescription($description)
    {
        $this->data['shippingDescription'] = $description;
        return $this;
    }

    public function getCustomerAddressId()
    {
        return $this->data['customerAddressId'] ?? null;
    }

    public function setCustomerAddressId($customerAddressId)
    {
        $this->data['customerAddressId'] = $customerAddressId;
        return $this;
    }

    public function getData($key = '', $index = null)
    {
        if ($key === '') {
            return $this->data;
        }
        return $this->data[$key] ?? null;
    }

    public function setData($key, $value = null)
    {
        if (is_array($key)) {
            $this->data = array_merge($this->data, $key);
        } else {
            $this->data[$key] = $value;
        }
        return $this;
    }

    public function getCountryId()
    {
        return $this->data['countryId'] ?? null;
    }

    public function setCountryId($countryId)
    {
        $this->data['countryId'] = $countryId;
        return $this;
    }

    public function getShippingRateByCode($code)
    {
        // If a specific code is set, return it; otherwise return the default rate
        if (isset($this->data['shippingRates'][$code])) {
            return $this->data['shippingRates'][$code];
        }
        // Return the default rate for any code
        return $this->data['defaultShippingRate'] ?? null;
    }

    public function setShippingRateByCode($code, $rate)
    {
        $this->data['shippingRates'][$code] = $rate;
        return $this;
    }

    public function setDefaultShippingRate($rate)
    {
        $this->data['defaultShippingRate'] = $rate;
        return $this;
    }

    public function setCollectShippingRates($value)
    {
        $this->data['collectShippingRates'] = $value;
        return $this;
    }

    public function collectShippingRates()
    {
        return $this;
    }
}

