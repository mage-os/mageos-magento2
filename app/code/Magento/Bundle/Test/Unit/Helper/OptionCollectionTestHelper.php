<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Bundle\Test\Unit\Helper;

use Magento\Bundle\Model\ResourceModel\Option\Collection;

/**
 * Test helper for Magento\Bundle\Model\ResourceModel\Option\Collection
 *
 * Extends Collection to add custom methods for testing
 */
class OptionCollectionTestHelper extends Collection
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
     * Get resource collection for testing
     *
     * @return mixed
     */
    public function getResourceCollection()
    {
        return $this->data['resource_collection'] ?? null;
    }

    /**
     * Set resource collection for testing
     *
     * @param mixed $collection
     * @return self
     */
    public function setResourceCollection($collection): self
    {
        $this->data['resource_collection'] = $collection;
        return $this;
    }
}
