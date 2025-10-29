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
     * @var string|null
     */
    private $email = null;

    /**
     * @var int|null
     */
    private $websiteId = null;

    /**
     * @var int|null
     */
    private $storeId = null;

    /**
     * Constructor that skips parent dependencies
     */
    public function __construct()
    {
        parent::__construct([]);
    }

    // Implement CustomerInterface methods with proper state management
    public function getId() { return $this->testData['id'] ?? null; }
    public function setId($id) { $this->testData['id'] = $id; return $this; }
    public function getGroupId() { return $this->groupId; }
    public function setGroupId($groupId) { $this->groupId = $groupId; return $this; }
    public function getDefaultBilling() { return $this->testData['default_billing'] ?? null; }
    public function setDefaultBilling($defaultBilling) { $this->testData['default_billing'] = $defaultBilling; return $this; }
    public function getDefaultShipping() { return $this->testData['default_shipping'] ?? null; }
    public function setDefaultShipping($defaultShipping) { $this->testData['default_shipping'] = $defaultShipping; return $this; }
    public function getConfirmation() { return $this->testData['confirmation'] ?? null; }
    public function setConfirmation($confirmation) { $this->testData['confirmation'] = $confirmation; return $this; }
    public function getCreatedAt() { return $this->testData['created_at'] ?? null; }
    public function setCreatedAt($createdAt) { $this->testData['created_at'] = $createdAt; return $this; }
    public function getUpdatedAt() { return $this->testData['updated_at'] ?? null; }
    public function setUpdatedAt($updatedAt) { $this->testData['updated_at'] = $updatedAt; return $this; }
    public function getCreatedIn() { return $this->testData['created_in'] ?? null; }
    public function setCreatedIn($createdIn) { $this->testData['created_in'] = $createdIn; return $this; }
    public function getDob() { return $this->testData['dob'] ?? null; }
    public function setDob($dob) { $this->testData['dob'] = $dob; return $this; }
    public function getEmail() { return $this->email; }
    public function setEmail($email) { $this->email = $email; return $this; }
    public function getFirstname() { return $this->testData['firstname'] ?? null; }
    public function setFirstname($firstname) { $this->testData['firstname'] = $firstname; return $this; }
    public function getLastname() { return $this->testData['lastname'] ?? null; }
    public function setLastname($lastname) { $this->testData['lastname'] = $lastname; return $this; }
    public function getMiddlename() { return $this->testData['middlename'] ?? null; }
    public function setMiddlename($middlename) { $this->testData['middlename'] = $middlename; return $this; }
    public function getPrefix() { return $this->testData['prefix'] ?? null; }
    public function setPrefix($prefix) { $this->testData['prefix'] = $prefix; return $this; }
    public function getSuffix() { return $this->testData['suffix'] ?? null; }
    public function setSuffix($suffix) { $this->testData['suffix'] = $suffix; return $this; }
    public function getGender() { return $this->testData['gender'] ?? null; }
    public function setGender($gender) { $this->testData['gender'] = $gender; return $this; }
    public function getStoreId() { return $this->storeId; }
    public function setStoreId($storeId) { $this->storeId = $storeId; return $this; }
    public function getTaxvat() { return $this->testData['taxvat'] ?? null; }
    public function setTaxvat($taxvat) { $this->testData['taxvat'] = $taxvat; return $this; }
    public function getWebsiteId() { return $this->websiteId; }
    public function setWebsiteId($websiteId) { $this->websiteId = $websiteId; return $this; }
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

