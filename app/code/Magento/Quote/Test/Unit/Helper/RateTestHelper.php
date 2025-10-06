<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

use Magento\Quote\Model\Quote\Address\Rate;

/**
 * Helper class for creating a lightweight Rate model for tests.
 */
class RateTestHelper extends Rate
{
    /** @var string */
    private string $code;

    /** @var string */
    private string $carrier;

    /** @var string */
    private string $method;

    /**
     * Initialize helper with minimal required values.
     *
     * @param string $code
     * @param string $carrier
     * @param string $method
     */
    public function __construct(string $code = 'shipping_method', string $carrier = 'shipping_carrier', string $method = 'shipping_carrier')
    {
        // Intentionally skip parent constructor
        $this->code = $code;
        $this->carrier = $carrier;
        $this->method = $method;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getCarrier(): string
    {
        return $this->carrier;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }
}





