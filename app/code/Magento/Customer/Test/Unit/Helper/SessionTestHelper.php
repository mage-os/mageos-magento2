<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Helper;

use Magento\Customer\Model\Session;

/**
 * Test helper for Session with custom methods
 */
class SessionTestHelper extends Session
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
     * Get before request params
     *
     * @return array|null
     */
    public function getBeforeRequestParams()
    {
        return $this->testData['before_request_params'] ?? null;
    }

    /**
     * Set before request params
     *
     * @param array $params
     * @return $this
     */
    public function setBeforeRequestParams(array $params)
    {
        $this->testData['before_request_params'] = $params;
        return $this;
    }

    /**
     * Set customer ID
     *
     * @param int|null $customerId
     * @return $this
     */
    public function setCustomerId($customerId)
    {
        $this->testData['customer_id'] = $customerId;
        return $this;
    }

    /**
     * Get customer ID
     *
     * @return int|null
     */
    public function getCustomerId()
    {
        return $this->testData['customer_id'] ?? null;
    }

    /**
     * Set address form data
     *
     * @param array|null $data
     * @return $this
     */
    public function setAddressFormData($data)
    {
        $this->testData['address_form_data'] = $data;
        return $this;
    }

    /**
     * Get address form data
     *
     * @return array|null
     */
    public function getAddressFormData()
    {
        return $this->testData['address_form_data'] ?? null;
    }

    /**
     * Unset address form data
     *
     * @return $this
     */
    public function unsAddressFormData()
    {
        unset($this->testData['address_form_data']);
        return $this;
    }
}
