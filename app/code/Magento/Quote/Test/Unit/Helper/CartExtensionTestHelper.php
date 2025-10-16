<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

use Magento\Quote\Api\Data\CartExtensionInterface;

/**
 * Test helper for CartExtension to support dynamic getNegotiableQuote/setNegotiableQuote methods
 */
class CartExtensionTestHelper implements CartExtensionInterface
{
    /**
     * @var \Magento\NegotiableQuote\Api\Data\NegotiableQuoteInterface|null
     */
    private $negotiableQuote;

    /**
     * @var array
     */
    private $shippingAssignments = [];

    /**
     * @var int|null
     */
    private $companyId;

    /**
     * Get negotiable quote
     *
     * @return \Magento\NegotiableQuote\Api\Data\NegotiableQuoteInterface|null
     */
    public function getNegotiableQuote()
    {
        return $this->negotiableQuote;
    }

    /**
     * Set negotiable quote
     *
     * @param \Magento\NegotiableQuote\Api\Data\NegotiableQuoteInterface|null $negotiableQuote
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
     * @return \Magento\Quote\Api\Data\ShippingAssignmentInterface[]|null
     */
    public function getShippingAssignments()
    {
        return $this->shippingAssignments;
    }

    /**
     * Set shipping assignments
     *
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface[] $shippingAssignments
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
     * @return int|null
     */
    public function getCompanyId()
    {
        return $this->companyId;
    }

    /**
     * Set company ID
     *
     * @param int|null $companyId
     * @return $this
     */
    public function setCompanyId($companyId)
    {
        $this->companyId = $companyId;
        return $this;
    }
}

