<?php
/**
 * Copyright 2020 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Helper;

use Magento\Customer\Api\Data\CustomerInterface;

/**
 * Test helper for CustomerInterface with custom methods
 */
class CustomerInterfaceTestHelper implements CustomerInterface
{
    /**
     * @var array<string, mixed>
     */
    private array $testData = [];

    /**
     * Set data
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function setData($key, $value = null)
    {
        $this->testData[$key] = $value;
        return $this;
    }

    /**
     * Get data
     *
     * @param string $key
     * @return mixed
     */
    public function getData($key = null)
    {
        if ($key === null) {
            return $this->testData;
        }
        return $this->testData[$key] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->testData['id'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setId($id)
    {
        $this->testData['id'] = $id;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getGroupId()
    {
        return $this->testData['group_id'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setGroupId($groupId)
    {
        $this->testData['group_id'] = $groupId;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDefaultBilling()
    {
        return $this->testData['default_billing'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setDefaultBilling($defaultBilling)
    {
        $this->testData['default_billing'] = $defaultBilling;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDefaultShipping()
    {
        return $this->testData['default_shipping'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setDefaultShipping($defaultShipping)
    {
        $this->testData['default_shipping'] = $defaultShipping;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getConfirmation()
    {
        return $this->testData['confirmation'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setConfirmation($confirmation)
    {
        $this->testData['confirmation'] = $confirmation;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCreatedAt()
    {
        return $this->testData['created_at'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setCreatedAt($createdAt)
    {
        $this->testData['created_at'] = $createdAt;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getUpdatedAt()
    {
        return $this->testData['updated_at'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->testData['updated_at'] = $updatedAt;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCreatedIn()
    {
        return $this->testData['created_in'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setCreatedIn($createdIn)
    {
        $this->testData['created_in'] = $createdIn;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDob()
    {
        return $this->testData['dob'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setDob($dob)
    {
        $this->testData['dob'] = $dob;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getEmail()
    {
        return $this->testData['email'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setEmail($email)
    {
        $this->testData['email'] = $email;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getFirstname()
    {
        return $this->testData['firstname'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setFirstname($firstname)
    {
        $this->testData['firstname'] = $firstname;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getLastname()
    {
        return $this->testData['lastname'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setLastname($lastname)
    {
        $this->testData['lastname'] = $lastname;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getMiddlename()
    {
        return $this->testData['middlename'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setMiddlename($middlename)
    {
        $this->testData['middlename'] = $middlename;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPrefix()
    {
        return $this->testData['prefix'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setPrefix($prefix)
    {
        $this->testData['prefix'] = $prefix;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSuffix()
    {
        return $this->testData['suffix'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setSuffix($suffix)
    {
        $this->testData['suffix'] = $suffix;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getGender()
    {
        return $this->testData['gender'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setGender($gender)
    {
        $this->testData['gender'] = $gender;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getStoreId()
    {
        return $this->testData['store_id'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setStoreId($storeId)
    {
        $this->testData['store_id'] = $storeId;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTaxvat()
    {
        return $this->testData['taxvat'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setTaxvat($taxvat)
    {
        $this->testData['taxvat'] = $taxvat;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getWebsiteId()
    {
        return $this->testData['website_id'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setWebsiteId($websiteId)
    {
        $this->testData['website_id'] = $websiteId;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAddresses()
    {
        return $this->testData['addresses'] ?? [];
    }

    /**
     * @inheritdoc
     */
    public function setAddresses(array $addresses = null)
    {
        $this->testData['addresses'] = $addresses;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDisableAutoGroupChange()
    {
        return $this->testData['disable_auto_group_change'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setDisableAutoGroupChange($disableAutoGroupChange)
    {
        $this->testData['disable_auto_group_change'] = $disableAutoGroupChange;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getExtensionAttributes()
    {
        return $this->testData['extension_attributes'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes(
        \Magento\Customer\Api\Data\CustomerExtensionInterface $extensionAttributes
    ) {
        $this->testData['extension_attributes'] = $extensionAttributes;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCustomAttributes()
    {
        return $this->testData['custom_attributes'] ?? [];
    }

    /**
     * @inheritdoc
     */
    public function setCustomAttributes(array $attributes)
    {
        $this->testData['custom_attributes'] = $attributes;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCustomAttribute($attributeCode)
    {
        return $this->testData['custom_attributes'][$attributeCode] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setCustomAttribute($attributeCode, $attributeValue)
    {
        $this->testData['custom_attributes'][$attributeCode] = $attributeValue;
        return $this;
    }
}
