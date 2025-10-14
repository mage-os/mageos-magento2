<?php
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Helper;

use Magento\Backend\Model\Session\Quote;

/**
 * Test helper for Magento\Backend\Model\Session\Quote
 *
 * WHY THIS HELPER IS REQUIRED:
 * - Parent Quote (Backend\Model\Session\Quote) has complex constructor requiring 14+ dependencies
 * - getCurrencyId() does NOT exist in parent - this is a custom test method
 * - Cannot use createPartialMock() to mock non-existent methods in PHPUnit 12
 * - Provides simple currency ID storage for unit tests
 *
 * Used By:
 * - Magento\NegotiableQuote\Test\Unit\Plugin\Quote\Model\QuoteAdminhtmlPluginTest
 */
class QuoteSessionTestHelper extends Quote
{
    /**
     * @var string|null
     */
    private $currencyId = null;

    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get currency ID
     *
     * This method does NOT exist in parent Quote class.
     *
     * @return string|null
     */
    public function getCurrencyId()
    {
        return $this->currencyId;
    }

    /**
     * Set currency ID
     *
     * This method does NOT exist in parent Quote class.
     *
     * @param string|null $currencyId
     * @return $this
     */
    public function setCurrencyId($currencyId)
    {
        $this->currencyId = $currencyId;
        return $this;
    }
}

