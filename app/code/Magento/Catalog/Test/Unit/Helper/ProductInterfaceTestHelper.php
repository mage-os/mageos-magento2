<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Model\Product;

/**
 * Test helper for ProductInterface
 *
 * This helper extends the concrete Product class to provide
 * test-specific functionality without dependency injection issues.
 * 
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class ProductInterfaceTestHelper extends Product
{
    /**
     * @var array
     */
    private $customAttributes = [];

    /**
     * Constructor that skips parent initialization
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get custom attribute value
     *
     * @param string $attributeCode
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getCustomAttribute($attributeCode)
    {
        return $this->customAttributes[$attributeCode] ?? null;
    }

    /**
     * Set custom attribute for testing
     *
     * @param string $attributeCode
     * @param mixed $attribute
     * @return $this
     */
    public function setCustomAttributeForTest($attributeCode, $attribute)
    {
        $this->customAttributes[$attributeCode] = $attribute;
        return $this;
    }

    /**
     * Initialize resources
     *
     * @return void
     */
    protected function _construct()
    {
        // Mock implementation - no actual resource initialization needed
    }
}
