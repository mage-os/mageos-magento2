<?php
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

use Magento\Quote\Api\Data\TotalsItemExtension;
use Magento\NegotiableQuote\Api\Data\NegotiableQuoteItemTotalsInterface;

/**
 * Test helper for Magento\Quote\Api\Data\TotalsItemExtension
 *
 * This helper extends the dynamically generated TotalsItemExtension class, providing
 * explicit methods for testing negotiable quote item totals functionality.
 *
 * WHY THIS HELPER IS REQUIRED:
 * - TotalsItemExtension is a dynamically generated extension attributes interface.
 * - Methods like getNegotiableQuoteItemTotals() and setNegotiableQuoteItemTotals() are added dynamically at runtime.
 * - Cannot use createPartialMock() on dynamically generated methods.
 * - Provides explicit implementation for testing negotiable quote item totals functionality.
 *
 * Used By: Multiple test files in Magento_NegotiableQuote module.
 */
class TotalsItemExtensionTestHelper extends TotalsItemExtension
{
    /**
     * @var NegotiableQuoteItemTotalsInterface|null
     */
    private $negotiableQuoteItemTotals;

    /**
     * Get negotiable quote item totals
     *
     * @return NegotiableQuoteItemTotalsInterface|null
     */
    public function getNegotiableQuoteItemTotals()
    {
        return $this->negotiableQuoteItemTotals;
    }

    /**
     * Set negotiable quote item totals
     *
     * @param NegotiableQuoteItemTotalsInterface $negotiableQuoteItemTotals
     * @return $this
     */
    public function setNegotiableQuoteItemTotals(NegotiableQuoteItemTotalsInterface $negotiableQuoteItemTotals)
    {
        $this->negotiableQuoteItemTotals = $negotiableQuoteItemTotals;
        return $this;
    }
}

