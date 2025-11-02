<?php
/**
 * Copyright 2025 Adobe
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
     * Set ID
     *
     * @param int|null $id
     * @return $this
     */
    public function setId($id): self
    {
        $this->testData['id'] = $id;
        return $this;
    }

    /**
     * Get confirmation
     *
     * @return string|null
     */
    public function getConfirmation()
    {
        return $this->testData['confirmation'] ?? null;
    }

    /**
     * Set confirmation
     *
     * @param string|null $confirmation
     * @return $this
     */
    public function setConfirmation($confirmation): self
    {
        $this->testData['confirmation'] = $confirmation;
        return $this;
    }

    /**
     * Get email
     *
     * @return string|null
     */
    public function getEmail()
    {
        return $this->testData['email'] ?? null;
    }

    /**
     * Set email
     *
     * @param string|null $email
     * @return $this
     */
    public function setEmail($email): self
    {
        $this->testData['email'] = $email;
        return $this;
    }

    /**
     * Get website ID
     *
     * @return int|null
     */
    public function getWebsiteId()
    {
        return $this->testData['website_id'] ?? null;
    }

    /**
     * Set website ID
     *
     * @param int|null $websiteId
     * @return $this
     */
    public function setWebsiteId($websiteId): self
    {
        $this->testData['website_id'] = $websiteId;
        return $this;
    }

    /**
     * Update data
     *
     * @param mixed $customer
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function updateData($customer): self
    {
        // Mock implementation
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
     * @param int|null $storeId
     * @return $this
     */
    public function setStoreId($storeId): self
    {
        $this->testData['store_id'] = $storeId;
        return $this;
    }

    /**
     * Set group ID
     *
     * @param int|null $groupId
     * @return $this
     */
    public function setGroupId($groupId): self
    {
        $this->testData['group_id'] = $groupId;
        return $this;
    }

    /**
     * Set attribute set ID
     *
     * @param int|null $attributeSetId
     * @return $this
     */
    public function setAttributeSetId($attributeSetId): self
    {
        $this->testData['attribute_set_id'] = $attributeSetId;
        return $this;
    }

    /**
     * Get attribute set ID
     *
     * @return int|null
     */
    public function getAttributeSetId()
    {
        return $this->testData['attribute_set_id'] ?? null;
    }

    /**
     * Set password reset token
     *
     * @param string|null $token
     * @return $this
     */
    public function setRpToken($token): self
    {
        $this->testData['rp_token'] = $token;
        return $this;
    }

    /**
     * Set password reset token created at
     *
     * @param string|null $createdAt
     * @return $this
     */
    public function setRpTokenCreatedAt($createdAt): self
    {
        $this->testData['rp_token_created_at'] = $createdAt;
        return $this;
    }

    /**
     * Set password hash
     *
     * @param string|null $hash
     * @return $this
     */
    public function setPasswordHash($hash): self
    {
        $this->testData['password_hash'] = $hash;
        return $this;
    }

    /**
     * Set failures number
     *
     * @param int $num
     * @return $this
     */
    public function setFailuresNum($num): self
    {
        $this->testData['failures_num'] = $num;
        return $this;
    }

    /**
     * Set first failure
     *
     * @param string|null $failure
     * @return $this
     */
    public function setFirstFailure($failure): self
    {
        $this->testData['first_failure'] = $failure;
        return $this;
    }

    /**
     * Set lock expires
     *
     * @param string|null $expires
     * @return $this
     */
    public function setLockExpires($expires): self
    {
        $this->testData['lock_expires'] = $expires;
        return $this;
    }

    /**
     * Get data model
     *
     * @return mixed
     */
    public function getDataModel()
    {
        return $this->testData['data_model'] ?? null;
    }

    /**
     * Set original data
     *
     * @param string|null $key
     * @param mixed $data
     * @return $this
     */
    public function setOrigData($key = null, $data = null): self
    {
        if ($key === null) {
            $this->testData['orig_data'] = $data;
        } else {
            $this->testData['orig_data'][$key] = $data;
        }
        return $this;
    }

    /**
     * Get collection
     *
     * @return mixed
     */
    public function getCollection()
    {
        return $this->testData['collection'] ?? null;
    }

    /**
     * Get disable auto group change
     *
     * @return bool|null
     */
    public function getDisableAutoGroupChange()
    {
        return $this->testData['disable_auto_group_change'] ?? null;
    }

    /**
     * Set disable auto group change
     *
     * @param bool $value
     * @return $this
     */
    public function setDisableAutoGroupChange(bool $value): self
    {
        $this->testData['disable_auto_group_change'] = $value;
    }
}
