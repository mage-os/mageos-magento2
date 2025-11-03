<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Helper;

use Magento\Customer\Api\Data\CustomerInterface;

/**
 * Test helper for CustomerInterface to support custom methods like __toArray
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class CustomerInterfaceTestHelper implements CustomerInterface
{
    /**
     * @var array
     */
    private array $data = [];

    /**
     * Convert to array
     *
     * @return array
     */
    public function __toArray(): array
    {
        return $this->data['__toArray'] ?? [];
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->data['id'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setId($id)
    {
        $this->data['id'] = $id;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getGroupId()
    {
        return $this->data['group_id'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setGroupId($groupId)
    {
        $this->data['group_id'] = $groupId;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDefaultBilling()
    {
        return $this->data['default_billing'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setDefaultBilling($defaultBilling)
    {
        $this->data['default_billing'] = $defaultBilling;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDefaultShipping()
    {
        return $this->data['default_shipping'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setDefaultShipping($defaultShipping)
    {
        $this->data['default_shipping'] = $defaultShipping;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getConfirmation()
    {
        return $this->data['confirmation'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setConfirmation($confirmation)
    {
        $this->data['confirmation'] = $confirmation;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCreatedAt()
    {
        return $this->data['created_at'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setCreatedAt($createdAt)
    {
        $this->data['created_at'] = $createdAt;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getUpdatedAt()
    {
        return $this->data['updated_at'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->data['updated_at'] = $updatedAt;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCreatedIn()
    {
        return $this->data['created_in'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setCreatedIn($createdIn)
    {
        $this->data['created_in'] = $createdIn;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDob()
    {
        return $this->data['dob'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setDob($dob)
    {
        $this->data['dob'] = $dob;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getEmail()
    {
        return $this->data['email'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setEmail($email)
    {
        $this->data['email'] = $email;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getFirstname()
    {
        return $this->data['firstname'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setFirstname($firstname)
    {
        $this->data['firstname'] = $firstname;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getLastname()
    {
        return $this->data['lastname'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setLastname($lastname)
    {
        $this->data['lastname'] = $lastname;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getMiddlename()
    {
        return $this->data['middlename'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setMiddlename($middlename)
    {
        $this->data['middlename'] = $middlename;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPrefix()
    {
        return $this->data['prefix'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setPrefix($prefix)
    {
        $this->data['prefix'] = $prefix;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSuffix()
    {
        return $this->data['suffix'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setSuffix($suffix)
    {
        $this->data['suffix'] = $suffix;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getGender()
    {
        return $this->data['gender'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setGender($gender)
    {
        $this->data['gender'] = $gender;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getStoreId()
    {
        return $this->data['store_id'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setStoreId($storeId)
    {
        $this->data['store_id'] = $storeId;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTaxvat()
    {
        return $this->data['taxvat'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setTaxvat($taxvat)
    {
        $this->data['taxvat'] = $taxvat;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getWebsiteId()
    {
        return $this->data['website_id'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setWebsiteId($websiteId)
    {
        $this->data['website_id'] = $websiteId;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAddresses()
    {
        return $this->data['addresses'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setAddresses(?array $addresses = null): self
    {
        $this->data['addresses'] = $addresses;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDisableAutoGroupChange()
    {
        return $this->data['disable_auto_group_change'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setDisableAutoGroupChange($disableAutoGroupChange)
    {
        $this->data['disable_auto_group_change'] = $disableAutoGroupChange;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getExtensionAttributes()
    {
        return $this->data['extension_attributes'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes(\Magento\Customer\Api\Data\CustomerExtensionInterface $extensionAttributes)
    {
        $this->data['extension_attributes'] = $extensionAttributes;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCustomAttributes()
    {
        return $this->data['custom_attributes'] ?? [];
    }

    /**
     * @inheritdoc
     */
    public function setCustomAttributes(array $attributes)
    {
        $this->data['custom_attributes'] = $attributes;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCustomAttribute($attributeCode)
    {
        return $this->data['custom_attributes'][$attributeCode] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setCustomAttribute($attributeCode, $attributeValue)
    {
        $this->data['custom_attributes'][$attributeCode] = $attributeValue;
        return $this;
    }

    /**
     * Set data array
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function setData($key, $value = null)
    {
        if ($value === null && is_array($key)) {
            $this->data = $key;
        } else {
            $this->data[$key] = $value;
        }
        return $this;
    }
}
