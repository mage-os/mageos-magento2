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
class CustomerTestHelper extends Customer
{
    /**
     * @var array<string, mixed>
     */
    private array $testData = [];

    /**
     * Constructor that skips parent to avoid dependency injection
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Set default billing
     *
     * @param int|string|null $value
     * @return $this
     */
    public function setDefaultBilling($value): self
    {
        $this->testData['default_billing'] = $value;
        return $this;
    }

    /**
     * Set default shipping
     *
     * @param int|string|null $value
     * @return $this
     */
    public function setDefaultShipping($value): self
    {
        $this->testData['default_shipping'] = $value;
        return $this;
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
     * Save customer
     *
     * @return $this
     */
    public function save()
    {
        return $this;
    }

    /**
     * Load customer
     *
     * @param int|string $modelId
     * @param string|null $field
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function load($modelId, $field = null)
    {
        return $this;
    }

    /**
     * Get resource
     *
     * @return mixed
     */
    public function getResource()
    {
        return $this->testData['resource'] ?? null;
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->testData['id'] ?? null;
    }

    /**
     * Get default shipping address
     *
     * @return mixed
     */
    public function getDefaultShippingAddress()
    {
        return $this->testData['default_shipping_address'] ?? null;
    }

    /**
     * Get default billing address
     *
     * @return mixed
     */
    public function getDefaultBillingAddress()
    {
        return $this->testData['default_billing_address'] ?? null;
    }

    /**
     * Get default billing
     *
     * @return int|string|null
     */
    public function getDefaultBilling()
    {
        return $this->testData['default_billing'] ?? null;
    }

    /**
     * Get default shipping
     *
     * @return int|string|null
     */
    public function getDefaultShipping()
    {
        return $this->testData['default_shipping'] ?? null;
    }

    /**
     * Get store ID
     *
     * @return int|null
     */
    public function getStoreId()
    {
        return $this->testData['store_id'] ?? null;
    }

    /**
     * Set store ID
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId(int $storeId): self
    {
        $this->testData['store_id'] = $storeId;
        return $this;
    }

    /**
     * Set group ID
     *
     * @param int $groupId
     * @return $this
     */
    public function setGroupId(int $groupId): self
    {
        $this->testData['group_id'] = $groupId;
        return $this;
    }
}
