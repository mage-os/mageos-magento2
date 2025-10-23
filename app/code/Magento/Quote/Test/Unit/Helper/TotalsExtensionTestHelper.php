<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

use Magento\Quote\Api\Data\TotalsExtensionInterface;

/**
 * Test helper for TotalsExtension to support extension attribute methods
 */
class TotalsExtensionTestHelper implements TotalsExtensionInterface
{
    /**
     * @var \Magento\NegotiableQuote\Api\Data\NegotiableQuoteTotalsInterface|null
     */
    private $negotiableQuoteTotals;

    /**
     * @var string|null
     */
    private $couponLabel;

    /**
     * @var float|null
     */
    private $rewardPointsBalance;

    /**
     * @var float|null
     */
    private $rewardCurrencyAmount;

    /**
     * @var float|null
     */
    private $baseRewardCurrencyAmount;

    /**
     * @var string[]|null
     */
    private $couponCodes;

    /**
     * @var string[]|null
     */
    private $couponsLabels;

    /**
     * Get negotiable quote totals
     *
     * @return \Magento\NegotiableQuote\Api\Data\NegotiableQuoteTotalsInterface|null
     */
    public function getNegotiableQuoteTotals()
    {
        return $this->negotiableQuoteTotals;
    }

    /**
     * Set negotiable quote totals
     *
     * @param \Magento\NegotiableQuote\Api\Data\NegotiableQuoteTotalsInterface|null $totals
     * @return $this
     */
    public function setNegotiableQuoteTotals($totals)
    {
        $this->negotiableQuoteTotals = $totals;
        return $this;
    }

    /**
     * Get coupon label
     *
     * @return string|null
     */
    public function getCouponLabel()
    {
        return $this->couponLabel;
    }

    /**
     * Set coupon label
     *
     * @param string|null $couponLabel
     * @return $this
     */
    public function setCouponLabel($couponLabel)
    {
        $this->couponLabel = $couponLabel;
        return $this;
    }

    /**
     * Get reward points balance
     *
     * @return float|null
     */
    public function getRewardPointsBalance()
    {
        return $this->rewardPointsBalance;
    }

    /**
     * Set reward points balance
     *
     * @param float|null $rewardPointsBalance
     * @return $this
     */
    public function setRewardPointsBalance($rewardPointsBalance)
    {
        $this->rewardPointsBalance = $rewardPointsBalance;
        return $this;
    }

    /**
     * Get reward currency amount
     *
     * @return float|null
     */
    public function getRewardCurrencyAmount()
    {
        return $this->rewardCurrencyAmount;
    }

    /**
     * Set reward currency amount
     *
     * @param float|null $rewardCurrencyAmount
     * @return $this
     */
    public function setRewardCurrencyAmount($rewardCurrencyAmount)
    {
        $this->rewardCurrencyAmount = $rewardCurrencyAmount;
        return $this;
    }

    /**
     * Get base reward currency amount
     *
     * @return float|null
     */
    public function getBaseRewardCurrencyAmount()
    {
        return $this->baseRewardCurrencyAmount;
    }

    /**
     * Set base reward currency amount
     *
     * @param float|null $baseRewardCurrencyAmount
     * @return $this
     */
    public function setBaseRewardCurrencyAmount($baseRewardCurrencyAmount)
    {
        $this->baseRewardCurrencyAmount = $baseRewardCurrencyAmount;
        return $this;
    }

    /**
     * Get coupon codes
     *
     * @return string[]|null
     */
    public function getCouponCodes()
    {
        return $this->couponCodes;
    }

    /**
     * Set coupon codes
     *
     * @param string[]|null $couponCodes
     * @return $this
     */
    public function setCouponCodes($couponCodes)
    {
        $this->couponCodes = $couponCodes;
        return $this;
    }

    /**
     * Get coupons labels
     *
     * @return string[]|null
     */
    public function getCouponsLabels()
    {
        return $this->couponsLabels;
    }

    /**
     * Set coupons labels
     *
     * @param string[]|null $couponsLabels
     * @return $this
     */
    public function setCouponsLabels($couponsLabels)
    {
        $this->couponsLabels = $couponsLabels;
        return $this;
    }
}
