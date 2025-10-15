<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Model\ResourceModel\Eav\Attribute;

/**
 * Test helper for Catalog EAV Attribute
 *
 * Extends Attribute to add custom methods for testing
 */
class AttributeTestHelper extends Attribute
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * Constructor - skip parent to avoid dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get attribute group code
     *
     * @return string|null
     */
    public function getAttributeGroupCode()
    {
        return $this->data['attribute_group_code'] ?? null;
    }

    /**
     * Set attribute group code
     *
     * @param string $code
     * @return $this
     */
    public function setAttributeGroupCode($code)
    {
        $this->data['attribute_group_code'] = $code;
        return $this;
    }
}
