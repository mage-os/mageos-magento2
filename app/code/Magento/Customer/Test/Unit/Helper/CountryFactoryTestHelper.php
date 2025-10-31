<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Helper;

use Magento\Directory\Model\CountryFactory;

/**
 * Test helper for CountryFactory with fluent interface support
 */
class CountryFactoryTestHelper extends CountryFactory
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
     * Create country (returns self for chaining)
     *
     * @param array $data
     * @return $this
     */
    public function create(array $data = [])
    {
        $this->testData = array_merge($this->testData, $data);
        return $this;
    }

    /**
     * Load by code (fluent interface)
     *
     * @param string $code
     * @return $this
     */
    public function loadByCode(string $code)
    {
        $this->testData['country_code'] = $code;
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
}
