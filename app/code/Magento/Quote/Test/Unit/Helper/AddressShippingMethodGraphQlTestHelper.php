<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

use Magento\Quote\Model\Quote\Address;

class AddressShippingMethodGraphQlTestHelper extends Address
{
    /**
     * @var float|int|string|null
     */
    private $amount;
    /**
     * @var string|null
     */
    private $method;
    /**
     * @var mixed
     */
    private $quote;
    /**
     * @var array
     */
    private $rates = [];

    public function __construct($amount = null, $method = null)
    {
        $this->amount = $amount;
        $this->method = $method;
    }

    public function setShippingAmountValue($value)
    {
        $this->amount = $value;
        return $this;
    }

    public function setMethodValue($value)
    {
        $this->method = $value;
        return $this;
    }

    public function getShippingAmount()
    {
        return $this->amount;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function setQuote($quote)
    {
        $this->quote = $quote;
        return $this;
    }

    public function getQuote()
    {
        return $this->quote;
    }

    public function setAllShippingRates(array $rates)
    {
        $this->rates = $rates;
        return $this;
    }

    public function getAllShippingRates()
    {
        return $this->rates;
    }

    public function getAttributes()
    {
        return [];
    }
}
