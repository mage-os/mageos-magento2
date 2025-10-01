<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Test\Unit\Helper;

use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Test helper for Magento\Framework\Pricing\PriceCurrencyInterface
 *
 * Implements PriceCurrencyInterface for testing with custom methods
 */
class PriceCurrencyInterfaceTestHelper implements PriceCurrencyInterface
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * Round price for testing
     *
     * @param float $price
     * @return float
     */
    public function roundPrice($price)
    {
        return $this->data['round_price_callback'] ?
            call_user_func($this->data['round_price_callback'], $price) :
            round($price, 2);
    }

    /**
     * Set round price callback for testing
     *
     * @param callable $callback
     * @return self
     */
    public function setRoundPriceCallback(callable $callback): self
    {
        $this->data['round_price_callback'] = $callback;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function convert($amount, $store = null, $currency = null)
    {
        return $amount;
    }

    /**
     * {@inheritdoc}
     */
    public function convertAndRound($amount, $store = null, $currency = null, $precision = PriceCurrencyInterface::DEFAULT_PRECISION)
    {
        return round($amount, $precision);
    }

    /**
     * {@inheritdoc}
     */
    public function format(
        $amount,
        $includeContainer = true,
        $precision = PriceCurrencyInterface::DEFAULT_PRECISION,
        $scope = null,
        $currency = null
    ) {
        return '$' . number_format($amount, $precision);
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrency($scope = null, $currency = null)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrencySymbol($scope = null, $currency = null)
    {
        return '$';
    }

    /**
     * {@inheritdoc}
     */
    public function round($price)
    {
        return round($price, 2);
    }

    /**
     * {@inheritdoc}
     */
    public function convertAndFormat(
        $amount,
        $includeContainer = true,
        $precision = PriceCurrencyInterface::DEFAULT_PRECISION,
        $scope = null,
        $currency = null
    ) {
        return '$' . number_format($amount, $precision);
    }
}
