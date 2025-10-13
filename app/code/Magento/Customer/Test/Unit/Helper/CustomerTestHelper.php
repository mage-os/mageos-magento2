<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Helper;

use Magento\Customer\Model\Customer;

/**
 * Test helper for Magento\Customer\Model\Customer
 *
 * This helper provides custom logic that cannot be achieved with standard PHPUnit mocks:
 * 1. getStoreId() with call counter - returns different values on consecutive calls
 *    (1 on first call, 2 on second call)
 * 2. load() - custom implementation that sets ID without requiring a resource
 *
 * All other methods are inherited from the parent Customer class or work via magic methods.
 */
class CustomerTestHelper extends Customer
{
    /**
     * @var int
     */
    private $callCount = 0;

    /**
     * @var mixed
     */
    private $id = null;

    public function __construct()
    {
        // Skip parent constructor to avoid dependencies
    }

    /**
     * Get store ID with call counter
     *
     * Custom logic: Returns 1 on first call, 2 on second call
     * This is used to test different store access scenarios
     *
     * @return int
     */
    public function getStoreId()
    {
        $this->callCount++;
        // Return store1 on first call, store2 on second call
        return $this->callCount === 1 ? 1 : 2;
    }

    /**
     * Get call count
     *
     * Custom logic: Returns the number of times getStoreId() was called
     *
     * @return int
     */
    public function getCallCount()
    {
        return $this->callCount;
    }

    /**
     * Reset call count
     *
     * Custom logic: Resets the call counter
     *
     * @return $this
     */
    public function resetCallCount()
    {
        $this->callCount = 0;
        return $this;
    }

    /**
     * Load customer
     *
     * Custom logic: Sets ID without requiring a resource (for testing)
     * Parent AbstractModel::load() requires a resource to be set
     *
     * @param mixed $id
     * @param mixed $field
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function load($id, $field = null)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get ID
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set ID
     *
     * @param mixed $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    // Other methods are inherited from parent Customer class:
    // - getWebsiteId(), setWebsiteId() - magic methods via @method
}
