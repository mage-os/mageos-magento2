<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

use Magento\Framework\Pricing\PriceCurrencyInterface;

class TotalRounderPriceCurrencyTestHelper implements PriceCurrencyInterface
{
    public function convert($amount, $store = null, $currency = null)
    {
        // Touch optional params for PHPMD and keep deterministic behavior
        if ($store !== null || $currency !== null) {
            // no-op
        }
        return $amount;
    }

    public function roundPrice($price)
    {
        return $price;
    }

    public function format(
        $amount,
        $includeContainer = true,
        $precision = PriceCurrencyInterface::DEFAULT_PRECISION,
        $scope = null,
        $currency = null
    ) {
        if ($includeContainer !== true ||
            $precision !== PriceCurrencyInterface::DEFAULT_PRECISION ||
            $scope !== null || $currency !== null
        ) {
            // no-op
        }
        return (string)$amount;
    }

    public function getCurrency(
        $scope = null,
        $currency = null
    ) {
        if ($scope !== null || $currency !== null) {
            // no-op
        }
        return null;
    }

    public function convertAndRound(
        $amount,
        $scope = null,
        $currency = null,
        $precision = self::DEFAULT_PRECISION
    ) {
        if ($scope !== null ||
            $currency !== null ||
            $precision !== self::DEFAULT_PRECISION
        ) {
            // no-op
        }
        return $amount;
    }

    public function convertAndFormat(
        $amount,
        $includeContainer = true,
        $precision = self::DEFAULT_PRECISION,
        $scope = null,
        $currency = null
    ) {
        if ($includeContainer !== true ||
            $precision !== self::DEFAULT_PRECISION ||
            $scope !== null ||
            $currency !== null
        ) {
            // no-op
        }
        return (string)$amount;
    }

    public function round($price)
    {
        return $price;
    }

    public function getCurrencySymbol($scope = null, $currency = null)
    {
        if ($scope !== null || $currency !== null) {
            // no-op
        }
        return '';
    }
}
