<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Helper;

use Magento\Customer\Model\Data\AttributeMetadata;

/**
 * Test helper for AttributeMetadata with custom methods
 */
class AttributeMetadataTestHelper extends AttributeMetadata
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
     * Mock __wakeup method
     *
     * @return void
     */
    public function __wakeup()
    {
        // Mock implementation
    }

    /**
     * Get attribute code
     *
     * @return string|null
     */
    public function getAttributeCode()
    {
        return $this->testData['attribute_code'] ?? null;
    }

    /**
     * Set attribute code
     *
     * @param string $attributeCode
     * @return $this
     */
    public function setAttributeCode($attributeCode)
    {
        $this->testData['attribute_code'] = $attributeCode;
        return $this;
    }

    /**
     * Get data model
     *
     * @return string|null
     */
    public function getDataModel()
    {
        return $this->testData['data_model'] ?? null;
    }

    /**
     * Set data model
     *
     * @param string $dataModel
     * @return $this
     */
    public function setDataModel($dataModel)
    {
        $this->testData['data_model'] = $dataModel;
        return $this;
    }
}

