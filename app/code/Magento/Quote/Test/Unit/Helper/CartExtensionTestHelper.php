<?php
/**
 * Copyright 2016 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

use Magento\Quote\Api\Data\CartExtension;
use Magento\NegotiableQuote\Api\Data\NegotiableQuoteInterface;

/**
 * Test helper for CartExtension
 *
 * This helper extends the concrete CartExtension class to provide
 * test-specific functionality without dependency injection issues.
 */
class CartExtensionTestHelper extends CartExtension
{
    /**
     * @var NegotiableQuoteInterface
     */
    private $negotiableQuote;

    /**
     * @var mixed
     */
    private $shippingAssignments;

    /**
     * @var mixed
     */
    private $companyId;

    /**
     * @var mixed
     */
    private $couponCodes;

    /**
     * @var mixed
     */
    private $negotiableQuoteItem;

    /**
     * Constructor that skips parent initialization
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get negotiable quote
     *
     * @return NegotiableQuoteInterface|null
     */
    public function getNegotiableQuote()
    {
        return $this->negotiableQuote;
    }

    /**
     * Set negotiable quote
     *
     * @param NegotiableQuoteInterface $negotiableQuote
     * @return $this
     */
    public function setNegotiableQuote($negotiableQuote)
    {
        $this->negotiableQuote = $negotiableQuote;
        return $this;
    }

    /**
     * Get shipping assignments
     *
     * @return mixed
     */
    public function getShippingAssignments()
    {
        return $this->shippingAssignments;
    }

    /**
     * Set shipping assignments
     *
     * @param mixed $shippingAssignments
     * @return $this
     */
    public function setShippingAssignments($shippingAssignments)
    {
        $this->shippingAssignments = $shippingAssignments;
        return $this;
    }

    /**
     * Get company ID
     *
     * @return mixed
     */
    public function getCompanyId()
    {
        return $this->companyId;
    }

    /**
     * Set company ID
     *
     * @param mixed $companyId
     * @return $this
     */
    public function setCompanyId($companyId)
    {
        $this->companyId = $companyId;
        return $this;
    }

    /**
     * Get coupon codes
     *
     * @return mixed
     */
    public function getCouponCodes()
    {
        return $this->couponCodes;
    }

    /**
     * Set coupon codes
     *
     * @param mixed $couponCodes
     * @return $this
     */
    public function setCouponCodes($couponCodes)
    {
        $this->couponCodes = $couponCodes;
        return $this;
    }

    /**
     * Get negotiable quote item
     *
     * @return mixed
     */
    public function getNegotiableQuoteItem()
    {
        return $this->negotiableQuoteItem;
    }

    /**
     * Set negotiable quote item
     *
     * @param mixed $negotiableQuoteItem
     * @return $this
     */
    public function setNegotiableQuoteItem($negotiableQuoteItem)
    {
        $this->negotiableQuoteItem = $negotiableQuoteItem;
        return $this;
    }
}
