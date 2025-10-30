<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Helper;

use Magento\Directory\Model\Region;

/**
 * Test helper for Region with custom methods
 */
class RegionTestHelper extends Region
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
     * Load region
     *
     * @param int|string $modelId
     * @param string|null $field
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function load($modelId, $field = null)
    {
        $this->testData['id'] = $modelId;
        return $this;
    }

    /**
     * Get ID
     *
     * @return int|string|null
     */
    public function getId()
    {
        return $this->testData['id'] ?? null;
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
}
