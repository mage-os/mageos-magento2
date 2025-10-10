<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

use Magento\Quote\Model\Quote\Address;

/**
 * Test helper for Quote Address to provide setPrevQuoteCustomerGroupId used in tests.
 */
class QuoteAddressTestHelper extends Address
{
    /** @var mixed */
    private $prevGroupId;
    /** @var array */
    private $vatData = [];
    /** @var string|null */
    private $addressType;

    /**
     * Constructor intentionally empty to skip parent dependencies.
     */
    public function __construct()
    {
        // Skip parent constructor
    }

    /**
     * Set previous quote customer group id for test assertions.
     *
     * @param mixed $groupId
     * @return $this
     */
    public function setPrevQuoteCustomerGroupId($groupId)
    {
        $this->prevGroupId = $groupId;
        return $this;
    }

    /**
     * VAT-related getters used in tests
     */
    public function getValidatedCountryCode()
    {
        return $this->vatData['validated_country_code'] ?? null;
    }

    public function getValidatedVatNumber()
    {
        return $this->vatData['validated_vat_number'] ?? null;
    }

    public function getVatIsValid()
    {
        return $this->vatData['vat_is_valid'] ?? null;
    }

    public function getVatRequestId()
    {
        return $this->vatData['vat_request_id'] ?? null;
    }

    public function getVatRequestDate()
    {
        return $this->vatData['vat_request_date'] ?? null;
    }

    public function getVatRequestSuccess()
    {
        return $this->vatData['vat_request_success'] ?? null;
    }

    /**
     * Address type getter used by VatValidator tests.
     *
     * @return string|null
     */
    public function getAddressType()
    {
        return $this->addressType;
    }

    /**
     * Address type setter for tests.
     *
     * @param string|null $type
     * @return $this
     */
    public function setAddressType($type)
    {
        $this->addressType = $type;
        return $this;
    }
}


