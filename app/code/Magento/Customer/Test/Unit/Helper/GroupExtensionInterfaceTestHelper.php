<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Helper;

use Magento\Customer\Api\Data\GroupExtensionInterface;

/**
 * Test helper for GroupExtensionInterface to support custom methods
 */
class GroupExtensionInterfaceTestHelper implements GroupExtensionInterface
{
    /**
     * @var array
     */
    private array $data = [];

    /**
     * Constructor - skip parent
     */
    public function __construct()
    {
        // Skip parent constructor
    }

    /**
     * Get excluded website IDs
     *
     * @return array|null
     */
    public function getExcludeWebsiteIds()
    {
        return $this->data['exclude_website_ids'] ?? null;
    }

    /**
     * Set excluded website IDs
     *
     * @param mixed $excludeWebsiteIds
     * @return $this
     */
    public function setExcludeWebsiteIds($excludeWebsiteIds): self
    {
        $this->data['exclude_website_ids'] = $excludeWebsiteIds;
        return $this;
    }

    /**
     * Get test dummy attribute
     *
     * @return string|null
     */
    public function getTestDummyAttribute(): ?string
    {
        return $this->data['test_dummy_attribute'] ?? null;
    }

    /**
     * Set test dummy attribute
     *
     * @param string $testDummyAttribute
     * @return $this
     */
    public function setTestDummyAttribute($testDummyAttribute): self
    {
        $this->data['test_dummy_attribute'] = $testDummyAttribute;
        return $this;
    }

    /**
     * Get test complex dummy attribute
     *
     * @return \Magento\Catalog\Api\Data\ProductAttributeInterface|null
     */
    public function getTestComplexDummyAttribute()
    {
        return $this->data['test_complex_dummy_attribute'] ?? null;
    }

    /**
     * Set test complex dummy attribute
     *
     * @param \Magento\Catalog\Api\Data\ProductAttributeInterface $testComplexDummyAttribute
     * @return $this
     */
    public function setTestComplexDummyAttribute($testComplexDummyAttribute): self
    {
        $this->data['test_complex_dummy_attribute'] = $testComplexDummyAttribute;
        return $this;
    }
}
