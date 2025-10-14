<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Checkout\Test\Unit\Helper;

use Magento\Store\Model\Store;

/**
 * Test helper store overriding formatPrice for predictable output.
 */
class StoreFormatPriceTestHelper extends Store
{
    /**
     * Constructor intentionally left empty for unit tests.
     */
    public function __construct()
    {
    }

    /**
     * Formats a price value as string for assertions.
     *
     * @param mixed $price
     * @param bool $includeContainer
     * @param int $precision
     * @param mixed $scope
     * @param mixed $currency
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function formatPrice(
        $price,
        $includeContainer = true,
        $precision = 2,
        $scope = null,
        $currency = null
    ) {
        return (string)$price;
    }
}
