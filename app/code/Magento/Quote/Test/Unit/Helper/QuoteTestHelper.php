<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

use Magento\Quote\Model\Quote;

/**
 * Test helper for Quote model used in unit tests to avoid complex constructor
 * dependencies and to emulate additional methods expected by legacy tests.
 */
class QuoteTestHelper extends Quote
{
    /**
     * @var array
     */
    private array $testData = [];

    /**
     * Constructor intentionally left empty to skip parent constructor.
     */
    public function __construct()
    {
        // Intentionally empty to skip parent constructor with complex dependencies
    }

    /**
     * Set shared store ids for the quote instance in tests.
     *
     * @param array $ids
     * @return $this
     */
    public function setSharedStoreIds($ids)
    {
        $this->testData['shared_store_ids'] = $ids;
        return $this;
    }

    /**
     * Get customer id set for the quote instance in tests.
     *
     * @return int|null
     */
    public function getCustomerId()
    {
        return $this->testData['customer_id'] ?? null;
    }

    /**
     * Set customer id for the quote instance in tests.
     *
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId($customerId)
    {
        $this->testData['customer_id'] = $customerId;
        return $this;
    }

    /**
     * Set customer group id for the quote instance in tests.
     *
     * @param mixed $groupId
     * @return $this
     */
    public function setCustomerGroupId($groupId)
    {
        $this->testData['customer_group_id'] = $groupId;
        return $this;
    }

    /**
     * Get customer group id for the quote instance in tests.
     *
     * @return mixed
     */
    public function getCustomerGroupId()
    {
        return $this->testData['customer_group_id'] ?? null;
    }

    /**
     * Set website for the quote in tests.
     *
     * @param mixed $website
     * @return $this
     */
    public function setWebsite($website)
    {
        $this->testData['website'] = $website;
        return $this;
    }

    /**
     * Get base currency code for tests.
     *
     * @return string|null
     */
    public function getBaseCurrencyCode()
    {
        return $this->testData['base_currency_code'] ?? null;
    }

    /**
     * Get quote currency code for tests.
     *
     * @return string|null
     */
    public function getQuoteCurrencyCode()
    {
        return $this->testData['quote_currency_code'] ?? null;
    }
}
