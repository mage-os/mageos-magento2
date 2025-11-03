<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Helper;

use Magento\Backend\Model\Session;

/**
 * Test helper for Backend Session with custom methods
 */
class SessionTestHelper extends Session
{
    /**
     * @var array|null
     */
    private $customerData = null;

    /**
     * Constructor that skips parent dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
        // Set storage to prevent session initialization errors
        $this->storage = new \ArrayObject();
    }

    /**
     * Get customer data (custom method for tests)
     *
     * @return array|null
     */
    public function getCustomerData()
    {
        return $this->customerData;
    }

    /**
     * Set customer data
     *
     * @param array|null $data
     * @return $this
     */
    public function setCustomerData($data): self
    {
        $this->customerData = $data;
        return $this;
    }

    /**
     * Set customer group data (custom method for tests)
     *
     * @param array|null $data
     * @return $this
     */
    public function setCustomerGroupData(?array $data): self
    {
        $this->customerData = $data; // Reuse the same storage
        return $this;
    }

    /**
     * Get customer form data (alias)
     *
     * @return array|null
     */
    public function getCustomerFormData(): ?array
    {
        return $this->customerData;
    }

    /**
     * Set customer form data (alias)
     *
     * @param array|null $data
     * @return $this
     */
    public function setCustomerFormData(?array $data): self
    {
        $this->customerData = $data;
        return $this;
    }

    /**
     * Unset customer data (custom method for tests)
     *
     * @return $this
     */
    public function unsCustomerData(): self
    {
        $this->customerData = null;
        return $this;
    }

    /**
     * Unset customer form data (custom method for tests)
     *
     * @return $this
     */
    public function unsCustomerFormData(): self
    {
        $this->customerData = null;
        return $this;
    }

    /**
     * Set is URL notice (custom method for tests)
     *
     * @param bool|null $flag
     * @return $this
     */
    public function setIsUrlNotice($flag = true): self
    {
        // Just return self for fluent interface
        return $this;
    }
}
