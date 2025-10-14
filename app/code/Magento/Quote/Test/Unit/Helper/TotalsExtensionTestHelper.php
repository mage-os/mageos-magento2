<?php
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

use Magento\Quote\Api\Data\TotalsExtension;
use Magento\NegotiableQuote\Api\Data\NegotiableQuoteTotalsInterface;

/**
 * Test helper for Magento\Quote\Api\Data\TotalsExtension
 *
 * This helper extends the dynamically generated TotalsExtension class, providing
 * explicit methods for testing negotiable quote totals functionality.
 *
 * WHY THIS HELPER IS REQUIRED:
 * - TotalsExtension is a dynamically generated extension attributes interface.
 * - Methods like getNegotiableQuoteTotals() and setNegotiableQuoteTotals() are added dynamically at runtime.
 * - Cannot use createPartialMock() on dynamically generated methods.
 * - Provides explicit implementation for testing negotiable quote totals functionality.
 *
 * Used By: Multiple test files in Magento_NegotiableQuote module.
 */
class TotalsExtensionTestHelper extends TotalsExtension
{
    /**
     * @var NegotiableQuoteTotalsInterface|null
     */
    private $negotiableQuoteTotals;

    /**
     * Get negotiable quote totals
     *
     * @return NegotiableQuoteTotalsInterface|null
     */
    public function getNegotiableQuoteTotals()
    {
        return $this->negotiableQuoteTotals;
    }

    /**
     * Set negotiable quote totals
     *
     * @param NegotiableQuoteTotalsInterface $negotiableQuoteTotals
     * @return $this
     */
    public function setNegotiableQuoteTotals(NegotiableQuoteTotalsInterface $negotiableQuoteTotals)
    {
        $this->negotiableQuoteTotals = $negotiableQuoteTotals;
        return $this;
    }
}

