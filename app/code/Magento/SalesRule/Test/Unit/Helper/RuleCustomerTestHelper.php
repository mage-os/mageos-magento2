<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\SalesRule\Test\Unit\Helper;

use Magento\SalesRule\Model\Rule\Customer;

/**
 * Test helper for Rule Customer
 *
 * This helper extends the concrete Customer class to provide
 * test-specific functionality without dependency injection issues.
 */
class RuleCustomerTestHelper extends Customer
{
    /**
     * @var int
     */
    private $timesUsed = 1;

    /**
     * @var int
     */
    private $id = 1;

    /**
     * Constructor that skips parent initialization
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Load by customer rule
     *
     * @param int $customerId
     * @param int $ruleId
     * @return $this
     */
    public function loadByCustomerRule($customerId, $ruleId)
    {
        return $this;
    }

    /**
     * Get ID
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set ID
     *
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get times used
     *
     * @return int
     */
    public function getTimesUsed()
    {
        return $this->timesUsed;
    }

    /**
     * Set times used
     *
     * @param int $timesUsed
     * @return $this
     */
    public function setTimesUsed($timesUsed)
    {
        $this->timesUsed = $timesUsed;
        return $this;
    }
}

