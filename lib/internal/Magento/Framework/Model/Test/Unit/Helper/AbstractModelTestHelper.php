<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Model\Test\Unit\Helper;

use Magento\Framework\Model\AbstractModel;

/**
 * Test helper for Magento\Framework\Model\AbstractModel
 *
 * Extends the AbstractModel class to add custom methods for testing
 */
class AbstractModelTestHelper extends AbstractModel
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependencies
    }

    /**
     * Get attribute ID for testing
     *
     * @return mixed
     */
    public function getAttributeId()
    {
        return $this->data['attribute_id'] ?? null;
    }

    /**
     * Set attribute ID for testing
     *
     * @param mixed $attributeId
     * @return self
     */
    public function setAttributeId($attributeId): self
    {
        $this->data['attribute_id'] = $attributeId;
        return $this;
    }

    /**
     * Override getId to work without constructor
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->data['id'] ?? null;
    }

    /**
     * Set ID for testing
     *
     * @param mixed $id
     * @return self
     */
    public function setId($id): self
    {
        $this->data['id'] = $id;
        return $this;
    }
}
