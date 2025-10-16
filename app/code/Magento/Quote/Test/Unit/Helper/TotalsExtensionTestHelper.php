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
}

