<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

use Magento\Quote\Model\Quote;

/**
 * Test helper providing getQuoteCurrencyCode()/setQuoteCurrencyCode() for Quote tests.
 */
class QuoteGetQuoteCurrencyCodeTestHelper extends Quote
{
    /**
     * @var string|null
     */
    private $quoteCurrencyCode = null;

    public function __construct()
    {
        // Intentionally empty to bypass parent constructor dependencies in tests
    }

    /**
     * @return string|null
     */
    public function getQuoteCurrencyCode()
    {
        return $this->quoteCurrencyCode;
    }

    /**
     * @param string $code
     * @return $this
     */
    public function setQuoteCurrencyCode($code)
    {
        $this->quoteCurrencyCode = $code;
        return $this;
    }
}
