<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

use Magento\Quote\Model\Quote\Address\Rate;

/**
 * Test helper for Address Rate
 *
 * This helper extends the concrete Rate class to provide
 * test-specific functionality without dependency injection issues.
 */
class AddressRateTestHelper extends Rate
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var float
     */
    private $price;

    /**
     * @var string
     */
    private $carrierTitle;

    /**
     * @var string
     */
    private $methodTitle;

    /**
     * Constructor that skips parent initialization
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get code
     *
     * @return string|null
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Get price
     *
     * @return float|null
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set price
     *
     * @param float $price
     * @return $this
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * Get carrier title
     *
     * @return string|null
     */
    public function getCarrierTitle()
    {
        return $this->carrierTitle;
    }

    /**
     * Set carrier title
     *
     * @param string $carrierTitle
     * @return $this
     */
    public function setCarrierTitle($carrierTitle)
    {
        $this->carrierTitle = $carrierTitle;
        return $this;
    }

    /**
     * Get method title
     *
     * @return string|null
     */
    public function getMethodTitle()
    {
        return $this->methodTitle;
    }

    /**
     * Set method title
     *
     * @param string $methodTitle
     * @return $this
     */
    public function setMethodTitle($methodTitle)
    {
        $this->methodTitle = $methodTitle;
        return $this;
    }

    /**
     * Set data
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function setData($key, $value = null)
    {
        return $this;
    }

    /**
     * Check if deleted
     *
     * @param bool|null $isDeleted
     * @return $this
     */
    public function isDeleted($isDeleted = null)
    {
        return $this;
    }
}

