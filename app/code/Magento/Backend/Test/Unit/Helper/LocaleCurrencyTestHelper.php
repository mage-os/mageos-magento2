<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Helper;

use Magento\Framework\Locale\Currency;

/**
 * Test helper for LocaleCurrency
 */
class LocaleCurrencyTestHelper extends LocaleCurrency
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * Skip parent constructor
     */
    public function __construct()
    {
        // Skip parent constructor
    }

    /**
     * toCurrency (custom method for testing)
     *
     * @return mixed
     */
    public function toCurrency()
    {
        return $this->data['toCurrency'] ?? null;
    }
}
