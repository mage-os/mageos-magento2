<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Helper;

use Magento\Customer\Model\Customer;

/**
 * Test helper for Customer with custom methods
 */
class CustomerModelTestHelper extends Customer
{
    /**
     * @var array<string, mixed>
     */
    private array $testData = [];

    /**
     * Constructor that skips parent to avoid dependency injection
     *
     * @SuppressWarnings(PHPMD.UselessOverridingMethod)
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Mock __wakeup method
     *
     * @return void
     */
    public function __wakeup()
    {
        // Mock implementation
    }

    /**
     * Set group ID
     *
     * @param int|null $groupId
     * @return $this
     */
    public function setGroupId($groupId)
    {
        $this->testData['group_id'] = $groupId;
        return $this;
    }

    /**
     * Get group ID
     *
     * @return int|null
     */
    public function getGroupId()
    {
        return $this->testData['group_id'] ?? null;
    }
}
