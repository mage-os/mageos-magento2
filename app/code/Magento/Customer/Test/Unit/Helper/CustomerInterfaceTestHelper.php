<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Helper;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\DataObject;

/**
 * Test helper for CustomerInterface with custom methods
 */
class CustomerInterfaceTestHelper extends DataObject implements CustomerInterface
{
    /**
     * @var array
     */
    private $testData = [];

    /**
     * @var int|null
     */
    private $groupId = null;

    /**
     * Constructor that skips parent dependencies
     */
    public function __construct()
    {
        parent::__construct([]);
    }

    // Implement CustomerInterface methods as stubs
    public function getId() { return $this->testData['id'] ?? null; }
    public function setId($id) { $this->testData['id'] = $id; return $this; }
    public function getGroupId() { return $this->groupId; }
    public function setGroupId($groupId) { $this->groupId = $groupId; return $this; }
    public function getDefaultBilling() { return null; }
    public function setDefaultBilling($defaultBilling) { return $this; }
    public function getDefaultShipping() { return null; }
    public function setDefaultShipping($defaultShipping) { return $this; }
    public function getConfirmation() { return null; }
    public function setConfirmation($confirmation) { return $this; }
    public function getCreatedAt() { return null; }
    public function setCreatedAt($createdAt) { return $this; }
    public function getUpdatedAt() { return null; }
    public function setUpdatedAt($updatedAt) { return $this; }
    public function getCreatedIn() { return null; }
    public function setCreatedIn($createdIn) { return $this; }
    public function getDob() { return null; }
    public function setDob($dob) { return $this; }
    public function getEmail() { return null; }
    public function setEmail($email) { return $this; }
    public function getFirstname() { return null; }
    public function setFirstname($firstname) { return $this; }
    public function getLastname() { return null; }
    public function setLastname($lastname) { return $this; }
    public function getMiddlename() { return null; }
    public function setMiddlename($middlename) { return $this; }
    public function getPrefix() { return null; }
    public function setPrefix($prefix) { return $this; }
    public function getSuffix() { return null; }
    public function setSuffix($suffix) { return $this; }
    public function getGender() { return null; }
    public function setGender($gender) { return $this; }
    public function getStoreId() { return null; }
    public function setStoreId($storeId) { return $this; }
    public function getTaxvat() { return null; }
    public function setTaxvat($taxvat) { return $this; }
    public function getWebsiteId() { return null; }
    public function setWebsiteId($websiteId) { return $this; }
    public function getAddresses() { return []; }
    public function setAddresses(array $addresses = null) { return $this; }
    public function getDisableAutoGroupChange() { return null; }
    public function setDisableAutoGroupChange($disableAutoGroupChange) { return $this; }
    public function getExtensionAttributes() { return null; }
    public function setExtensionAttributes(\Magento\Customer\Api\Data\CustomerExtensionInterface $extensionAttributes) { return $this; }
    public function getCustomAttributes() { return []; }
    public function getCustomAttribute($attributeCode) { return null; }
    public function setCustomAttributes(array $attributes) { return $this; }
    public function setCustomAttribute($attributeCode, $attributeValue) { return $this; }


    /**
     * Set data (custom method for tests)
     *
     * @param string|array $key
     * @param mixed $value
     * @return $this
     */
    public function setData($key, $value = null): self
    {
        if (is_array($key)) {
            $this->testData = $key;
        } else {
            $this->testData[$key] = $value;
        }
        return $this;
    }

    /**
     * Get data
     *
     * @param string $key
     * @param mixed $index
     * @return mixed
     */
    public function getData($key = '', $index = null)
    {
        if ($key === '') {
            return $this->testData;
        }
        if ($index !== null) {
            return $this->testData[$key][$index] ?? null;
        }
        return $this->testData[$key] ?? null;
    }
}

