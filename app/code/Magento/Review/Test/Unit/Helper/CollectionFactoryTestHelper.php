<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Review\Test\Unit\Helper;

use Magento\Review\Model\ResourceModel\Review\CollectionFactory;

/**
 * Test helper for Review Collection Factory
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class CollectionFactoryTestHelper extends CollectionFactory
{
    /**
     * @var mixed
     */
    private $collection = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Create collection
     *
     * @param array $data
     * @return mixed
     */
    public function create(array $data = [])
    {
        return $this->collection;
    }

    /**
     * Set collection
     *
     * @param mixed $collection
     * @return $this
     */
    public function setCollection($collection): self
    {
        $this->collection = $collection;
        return $this;
    }

    /**
     * Wakeup method
     *
     * @return $this
     */
    public function __wakeup()
    {
        return $this;
    }
}
