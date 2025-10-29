<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Eav\Test\Unit\Helper;

use Magento\Eav\Model\Entity\Attribute\Group;

/**
 * Test helper for AttributeGroupInterface providing extension attribute methods
 */
class AttributeGroupInterfaceTestHelper extends Group
{
    /**
     * @var string
     */
    private string $attributeGroupCode = '';

    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    public function getAttributeGroupCode()
    {
        return $this->attributeGroupCode;
    }

    public function setAttributeGroupCode($value)
    {
        $this->attributeGroupCode = $value;
        return $this;
    }
}