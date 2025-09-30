<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

use Magento\Quote\Model\Quote\Address;

/**
 * Test helper that provides a controllable \Magento\Quote\Model\Quote\Address
 * for shipping-related unit tests.
 */
class AddressShippingInfoTestHelper extends Address
{
    /** @var string|null */
    private $countryIdVal = null;

    /** @var string|null */
    private $shippingMethodVal = null;

    /** @var mixed */
    private $shippingRateByCodeVal = null;

    /** @var bool|null */
    private $collectShippingRatesFlag = null;

    /** @var int|float|null */
    private $shippingAmount = null;

    /** @var int|float|null */
    private $baseShippingAmount = null;

    /**
     * Override parent constructor; not needed for tests.
     */
    public function __construct()
    {
    }

    /**
     * @param string|null $value
     * @return $this
     */
    public function setCountryIdVal($value)
    {
        $this->countryIdVal = $value;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCountryId()
    {
        return $this->countryIdVal;
    }

    /**
     * @param string|null $value
     * @return $this
     */
    public function setShippingMethodVal($value)
    {
        $this->shippingMethodVal = $value;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getShippingMethod()
    {
        return $this->shippingMethodVal;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function setShippingRateByCodeVal($value)
    {
        $this->shippingRateByCodeVal = $value;
        return $this;
    }

    /**
     * @param string $code
     * @return mixed
     */
    public function getShippingRateByCode($code)
    {
        return $this->shippingRateByCodeVal;
    }

    /**
     * @param bool $flag
     * @return $this
     */
    public function setCollectShippingRates($flag)
    {
        $this->collectShippingRatesFlag = (bool)$flag;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getCollectShippingRatesFlag()
    {
        return $this->collectShippingRatesFlag;
    }

    /**
     * @param int|float $value
     * @param bool $alreadyExclTax
     * @return $this
     */
    public function setShippingAmount($value, $alreadyExclTax = false)
    {
        $this->shippingAmount = $value;
        return $this;
    }

    /**
     * @return int|float|null
     */
    public function getShippingAmount()
    {
        return $this->shippingAmount;
    }

    /**
     * @param int|float $value
     * @param bool $alreadyExclTax
     * @return $this
     */
    public function setBaseShippingAmount($value, $alreadyExclTax = false)
    {
        $this->baseShippingAmount = $value;
        return $this;
    }

    /**
     * @return int|float|null
     */
    public function getBaseShippingAmount()
    {
        return $this->baseShippingAmount;
    }

    /**
     * @param string $value
     * @param bool $alreadyExclTax
     * @return $this
     */
    public function setShippingMethod($value, $alreadyExclTax = false)
    {
        $this->shippingMethodVal = $value;
        return $this;
    }

    /**
     * No-op persistence for tests.
     *
     * @return $this
     */
    public function save()
    {
        return $this;
    }
}


