<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Helper;

use Magento\Customer\Model\Address;

class AddressTestHelper extends Address
{
    /**
     * @var array<string, mixed>
     */
    private $data = [];

    /**
     * Constructor - skip parent constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->data['id'] ?? null;
    }

    /**
     * Set ID
     *
     * @param int|null $id
     * @return $this
     */
    public function setId($id)
    {
        $this->data['id'] = $id;
        return $this;
    }

    /**
     * Get is default billing
     *
     * @return bool|null
     */
    public function getIsDefaultBilling()
    {
        return $this->data['is_default_billing'] ?? null;
    }

    /**
     * Set is default billing
     *
     * @param bool $value
     * @return $this
     */
    public function setIsDefaultBilling($value)
    {
        $this->data['is_default_billing'] = $value;
        return $this;
    }

    /**
     * Get is default shipping
     *
     * @return bool|null
     */
    public function getIsDefaultShipping()
    {
        return $this->data['is_default_shipping'] ?? null;
    }

    /**
     * Set is default shipping
     *
     * @param bool $value
     * @return $this
     */
    public function setIsDefaultShipping($value)
    {
        $this->data['is_default_shipping'] = $value;
        return $this;
    }

    /**
     * Set force process
     *
     * @param bool $value
     * @return $this
     */
    public function setForceProcess($value)
    {
        $this->data['force_process'] = $value;
        return $this;
    }

    /**
     * Get is customer save transaction
     *
     * @return bool|null
     */
    public function getIsCustomerSaveTransaction()
    {
        return $this->data['is_customer_save_transaction'] ?? null;
    }

    /**
     * Set is customer save transaction
     *
     * @param bool $value
     * @return $this
     */
    public function setIsCustomerSaveTransaction($value)
    {
        $this->data['is_customer_save_transaction'] = $value;
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
     * Get entity type ID
     *
     * @return int|null
     */
    public function getEntityTypeId()
    {
        return $this->data['entity_type_id'] ?? null;
    }

    /**
     * Check if has data changes
     *
     * @return bool
     */
    public function hasDataChanges()
    {
        return $this->data['has_data_changes'] ?? false;
    }

    /**
     * Validate before save
     *
     * @return $this
     */
    public function validateBeforeSave()
    {
        return $this;
    }

    /**
     * Before save
     *
     * @return $this
     */
    public function beforeSave()
    {
        return $this;
    }

    /**
     * After save
     *
     * @return $this
     */
    public function afterSave()
    {
        return $this;
    }

    /**
     * Check if save is allowed
     *
     * @return bool
     */
    public function isSaveAllowed()
    {
        return $this->data['is_save_allowed'] ?? true;
    }

    /**
     * Get country ID
     *
     * @return string|null
     */
    public function getCountryId()
    {
        return $this->data['country_id'] ?? null;
    }

    /**
     * Get firstname
     *
     * @return string|null
     */
    public function getFirstname()
    {
        return $this->data['firstname'] ?? null;
    }

    /**
     * Get lastname
     *
     * @return string|null
     */
    public function getLastname()
    {
        return $this->data['lastname'] ?? null;
    }

    /**
     * Get city
     *
     * @return string|null
     */
    public function getCity()
    {
        return $this->data['city'] ?? null;
    }

    /**
     * Get telephone
     *
     * @return string|null
     */
    public function getTelephone()
    {
        return $this->data['telephone'] ?? null;
    }

    /**
     * Get should ignore validation
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getShouldIgnoreValidation()
    {
        return $this->data['should_ignore_validation'] ?? false;
    }

    /**
     * Set should ignore validation
     *
     * @param bool $value
     * @return $this
     */
    public function setShouldIgnoreValidation($value)
    {
        $this->data['should_ignore_validation'] = $value;
        return $this;
    }

    /**
     * Set store ID
     *
     * @param int|null $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->data['store_id'] = $storeId;
        return $this;
    }

    /**
     * Get street line
     *
     * @param int $lineNumber
     * @return string|null
     */
    public function getStreetLine($lineNumber)
    {
        return $this->data['street_line_' . $lineNumber] ?? null;
    }

    /**
     * Get region ID
     *
     * @return int|null
     */
    public function getRegionId()
    {
        return $this->data['region_id'] ?? null;
    }

    /**
     * Get region
     *
     * @return string|null
     */
    public function getRegion()
    {
        return $this->data['region'] ?? null;
    }

    /**
     * Update data
     *
     * @param mixed $address
     * @return $this
     */
    public function updateData($address)
    {
        $this->data['updated_data'] = $address;
        return $this;
    }

    /**
     * Set customer
     *
     * @param mixed $customer
     * @return $this
     */
    public function setCustomer($customer)
    {
        $this->data['customer'] = $customer;
        return $this;
    }

    /**
     * Get country model
     *
     * @return mixed
     */
    public function getCountryModel()
    {
        return $this->data['country_model'] ?? null;
    }

    /**
     * Validate
     *
     * @return bool|array
     */
    public function validate()
    {
        return $this->data['validate_result'] ?? true;
    }

    /**
     * Save
     *
     * @return $this
     */
    public function save()
    {
        return $this;
    }

    /**
     * Get data model
     *
     * @param int|null $defaultBillingAddressId
     * @param int|null $defaultShippingAddressId
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getDataModel($defaultBillingAddressId = null, $defaultShippingAddressId = null)
    {
        return $this->data['data_model'] ?? null;
    }

    /**
     * Get customer ID
     *
     * @return int|null
     */
    public function getCustomerId()
    {
        return $this->data['customer_id'] ?? null;
    }

    /**
     * Get is primary billing
     *
     * @return mixed
     */
    public function getIsPrimaryBilling()
    {
        return $this->data['is_primary_billing'] ?? null;
    }

    /**
     * Get is primary shipping
     *
     * @return mixed
     */
    public function getIsPrimaryShipping()
    {
        return $this->data['is_primary_shipping'] ?? null;
    }

    /**
     * Get force process
     *
     * @return mixed
     */
    public function getForceProcess()
    {
        return $this->data['force_process'] ?? null;
    }

    /**
     * Get VAT ID
     *
     * @return mixed
     */
    public function getVatId()
    {
        return $this->data['vat_id'] ?? null;
    }

    /**
     * Set VAT validation result
     *
     * @param mixed $result
     * @return $this
     */
    public function setVatValidationResult($result): self
    {
        $this->data['vat_validation_result'] = $result;
        return $this;
    }
}
