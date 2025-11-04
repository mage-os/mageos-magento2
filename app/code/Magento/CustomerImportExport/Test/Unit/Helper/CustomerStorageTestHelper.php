<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CustomerImportExport\Test\Unit\Helper;

/**
 * Test helper for customer storage
 */
class CustomerStorageTestHelper
{
    /**
     * @var int|bool|null
     */
    private $customerId = null;

    /**
     * Get customer ID
     *
     * @return int|bool|null
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * Set customer ID
     *
     * @param int|bool|null $customerId
     * @return $this
     */
    public function setCustomerId($customerId): self
    {
        $this->customerId = $customerId;
        return $this;
    }

    /**
     * Prepare customers
     *
     * @return void
     */
    public function prepareCustomers(): void
    {
        // Stub implementation
    }

    /**
     * Add customer
     *
     * @param mixed $customer
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addCustomer($customer): self
    {
        // Stub implementation
        return $this;
    }
}
