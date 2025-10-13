<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Model\Cart;

use Magento\Quote\Model\Quote;

/**
 * Concrete double to expose magic currency methods for mocking in PHPUnit 12.
 */
class CartQuoteMagicDouble extends Quote
{
    /**
     * @var string
     */
    private $baseCurrencyCode = '';
    /**
     * @var string
     */
    private $quoteCurrencyCode = '';

    public function __construct()
    {
        // Intentionally skip parent constructor
    }

    public function getBaseCurrencyCode()
    {
        return $this->baseCurrencyCode;
    }

    public function setBaseCurrencyCode($code)
    {
        $this->baseCurrencyCode = $code;
        return $this;
    }

    public function getQuoteCurrencyCode()
    {
        return $this->quoteCurrencyCode;
    }

    public function setQuoteCurrencyCode($code)
    {
        $this->quoteCurrencyCode = $code;
        return $this;
    }
}
