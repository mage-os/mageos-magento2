<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Helper;

use Magento\Directory\Model\Country;

/**
 * Test helper for Country with custom methods
 */
class CountryTestHelper extends Country
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
     * Convert to option array
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return $this->testData['option_array'] ?? [];
    }

    /**
     * Set option array
     *
     * @param array $optionArray
     * @return $this
     */
    public function setOptionArray(array $optionArray): self
    {
        $this->testData['option_array'] = $optionArray;
        return $this;
    }

    /**
     * Set ID
     *
     * @param string|int $id
     * @return $this
     */
    public function setId($id)
    {
        $this->testData['id'] = $id;
        return $this;
    }

    /**
     * Get loaded region collection
     *
     * @return $this
     */
    public function getLoadedRegionCollection()
    {
        return $this;
    }

    /**
     * Mock for __wakeup
     *
     * @return void
     */
    public function __wakeup()
    {
        // Mock implementation
    }
}
