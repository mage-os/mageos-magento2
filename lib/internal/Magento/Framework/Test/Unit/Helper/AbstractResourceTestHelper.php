<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Test\Unit\Helper;

use Magento\Framework\Model\ResourceModel\AbstractResource;

/**
 * Test helper for AbstractResource with custom methods
 */
class AbstractResourceTestHelper extends AbstractResource
{
    /**
     * Constructor that skips parent dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependencies
    }

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        // Stub implementation for abstract method
    }

    /**
     * Get ID field name (custom method for tests)
     *
     * @return string|null
     */
    public function getIdFieldName()
    {
        return 'entity_id';
    }

    /**
     * Save in set including (custom method for tests)
     *
     * @param mixed $object
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function saveInSetIncluding($object): self
    {
        return $this;
    }

    /**
     * Get connection (abstract method implementation)
     *
     * @return mixed
     */
    public function getConnection()
    {
        return null;
    }
}
