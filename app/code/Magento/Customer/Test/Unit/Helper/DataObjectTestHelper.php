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
}
