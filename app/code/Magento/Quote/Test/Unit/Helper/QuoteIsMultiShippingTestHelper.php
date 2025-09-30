<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

use Magento\Quote\Model\Quote;

/**
 * Quote stub exposing getIsMultiShipping and getStoreId for tests.
 */
class QuoteIsMultiShippingTestHelper extends Quote
{
    /** @var bool */
    private $isMultiShipping;

    /** @var int */
    private $storeId;

    public function __construct(int $storeId = 1, bool $isMultiShipping = false)
    {
        $this->storeId = $storeId;
        $this->isMultiShipping = $isMultiShipping;
    }

    public function getIsMultiShipping()
    {
        return $this->isMultiShipping;
    }

    public function getStoreId()
    {
        return $this->storeId;
    }
}


