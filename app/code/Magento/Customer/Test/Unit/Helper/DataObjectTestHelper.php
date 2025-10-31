<?php
/**
 * Copyright 2016 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Helper;

use Magento\Framework\DataObject;

/**
 * Test helper for DataObject with custom methods
 */
class DataObjectTestHelper extends DataObject
{
    /**
     * @var array<string, mixed>
     */
    private array $testData = [];

    /**
     * Constructor that skips parent to avoid dependency injection
     *
     * @SuppressWarnings(PHPMD.UselessOverridingMethod)
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
        return $this->testData['id'] ?? null;
    }

    /**
     * Set ID
     *
     * @param int|null $id
     * @return $this
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    public function setTestId($id): self
    {
        $this->testData['id'] = $id;
        return $this;
    }

    /**
     * Get is default billing
     *
     * @return int|null
     */
    public function getIsDefaultBilling()
    {
        return $this->testData['is_default_billing'] ?? null;
    }

    /**
     * Set is default billing
     *
     * @param int $value
     * @return $this
     */
    public function setIsDefaultBilling(int $value): self
    {
        $this->testData['is_default_billing'] = $value;
        return $this;
    }

    /**
     * Get is default shipping
     *
     * @return int|null
     */
    public function getIsDefaultShipping()
    {
        return $this->testData['is_default_shipping'] ?? null;
    }

    /**
     * Set is default shipping
     *
     * @param int $value
     * @return $this
     */
    public function setIsDefaultShipping(int $value): self
    {
        $this->testData['is_default_shipping'] = $value;
        return $this;
    }

    /**
     * Get is customer save transaction
     *
     * @return bool|null
     */
    public function getIsCustomerSaveTransaction()
    {
        return $this->testData['is_customer_save_transaction'] ?? null;
    }

    /**
     * Set is customer save transaction
     *
     * @param bool $value
     * @return $this
     */
    public function setIsCustomerSaveTransaction(bool $value): self
    {
        $this->testData['is_customer_save_transaction'] = $value;
        return $this;
    }

    /**
     * Get country ID
     *
     * @return string|null
     */
    public function getCountryId()
    {
        return $this->testData['country_id'] ?? null;
    }

    /**
     * Set country ID
     *
     * @param string $countryId
     * @return $this
     */
    public function setCountryId(string $countryId): self
    {
        $this->testData['country_id'] = $countryId;
        return $this;
    }

    /**
     * Set region ID
     *
     * @param string|int $regionId
     * @return $this
     */
    public function setRegionId($regionId): self
    {
        $this->testData['region_id'] = $regionId;
        return $this;
    }

    /**
     * Set region
     *
     * @param string $region
     * @return $this
     */
    public function setRegion(string $region): self
    {
        $this->testData['region'] = $region;
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
     * @param int $websiteId
     * @return $this
     */
    public function setWebsiteId(int $websiteId): self
    {
        $this->testData['website_id'] = $websiteId;
        return $this;
    }

    /**
     * Get default billing
     *
     * @return mixed
     */
    public function getDefaultBilling()
    {
        return $this->testData['default_billing'] ?? null;
    }

    /**
     * Set default billing
     *
     * @param mixed $value
     * @return $this
     */
    public function setDefaultBilling($value): self
    {
        $this->testData['default_billing'] = $value;
        return $this;
    }

    /**
     * Unset default billing
     *
     * @return $this
     */
    public function unsetDefaultBilling(): self
    {
        unset($this->testData['default_billing']);
        return $this;
    }

    /**
     * Get addresses
     *
     * @return array
     */
    public function getAddresses(): array
    {
        return $this->testData['addresses'] ?? [];
    }

    /**
     * Set addresses
     *
     * @param array $addresses
     * @return $this
     */
    public function setAddresses(array $addresses): self
    {
        $this->testData['addresses'] = $addresses;
        return $this;
    }

    /**
     * Get post index
     *
     * @return mixed
     */
    public function getPostIndex()
    {
        return $this->testData['post_index'] ?? null;
    }

    /**
     * Set post index
     *
     * @param mixed $value
     * @return $this
     */
    public function setPostIndex($value): self
    {
        $this->testData['post_index'] = $value;
        return $this;
    }

    /**
     * Get password
     *
     * @return string|null
     */
    public function getPassword()
    {
        return $this->testData['password'] ?? null;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return $this
     */
    public function setPassword(string $password): self
    {
        $this->testData['password'] = $password;
        return $this;
    }

    /**
     * Get password confirm
     *
     * @return string|null
     */
    public function getPasswordConfirm()
    {
        return $this->testData['password_confirm'] ?? null;
    }

    /**
     * Set password confirm
     *
     * @param string $passwordConfirm
     * @return $this
     */
    public function setPasswordConfirm(string $passwordConfirm): self
    {
        $this->testData['password_confirm'] = $passwordConfirm;
        return $this;
    }

    /**
     * Set password hash
     *
     * @param string $passwordHash
     * @return $this
     */
    public function setPasswordHash(string $passwordHash): self
    {
        $this->testData['password_hash'] = $passwordHash;
        return $this;
    }

    /**
     * Hash password
     *
     * @return string|null
     */
    public function hashPassword()
    {
        return $this->testData['hashed_password'] ?? null;
    }

    /**
     * Set hashed password (for mock)
     *
     * @param string $hashedPassword
     * @return $this
     */
    public function setHashedPassword(string $hashedPassword): self
    {
        $this->testData['hashed_password'] = $hashedPassword;
        return $this;
    }

    /**
     * Get default shipping
     *
     * @return mixed
     */
    public function getDefaultShipping()
    {
        return $this->testData['default_shipping'] ?? null;
    }

    /**
     * Set default shipping
     *
     * @param mixed $value
     * @return $this
     */
    public function setDefaultShipping($value): self
    {
        $this->testData['default_shipping'] = $value;
        return $this;
    }

    /**
     * Unset default shipping
     *
     * @return $this
     */
    public function unsetDefaultShipping(): self
    {
        unset($this->testData['default_shipping']);
        return $this;
    }

    /**
     * Has store ID
     *
     * @return bool
     */
    public function hasStoreId(): bool
    {
        return isset($this->testData['store_id']);
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
     * @param int $storeId
     * @return $this
     */
    public function setStoreId(int $storeId): self
    {
        $this->testData['store_id'] = $storeId;
        return $this;
    }

    /**
     * Get name
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->testData['name'] ?? null;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->testData['name'] = $name;
        return $this;
    }

    /**
     * Get is valid
     *
     * @return bool|null
     */
    public function getIsValid()
    {
        return $this->testData['is_valid'] ?? null;
    }

    /**
     * Set is valid
     *
     * @param bool $value
     * @return $this
     */
    public function setIsValid(bool $value): self
    {
        $this->testData['is_valid'] = $value;
        return $this;
    }

    /**
     * Get request success
     *
     * @return bool|null
     */
    public function getRequestSuccess()
    {
        return $this->testData['request_success'] ?? null;
    }

    /**
     * Set request success
     *
     * @param bool $value
     * @return $this
     */
    public function setRequestSuccess(bool $value): self
    {
        $this->testData['request_success'] = $value;
        return $this;
    }
}
