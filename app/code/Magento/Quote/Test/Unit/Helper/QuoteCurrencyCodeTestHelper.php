<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

use Magento\Quote\Model\Quote;

class QuoteCurrencyCodeTestHelper extends Quote
{
    private $code = 'USD';

    public function __construct(string $code = 'USD')
    {
        $this->code = $code;
    }

    public function getQuoteCurrencyCode()
    {
        return $this->code;
    }
}






