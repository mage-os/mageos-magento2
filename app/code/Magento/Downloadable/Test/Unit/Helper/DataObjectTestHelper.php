<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Downloadable\Test\Unit\Helper;

use Magento\Framework\DataObject;

/**
 * Test helper class for DataObject with custom methods
 */
class DataObjectTestHelper extends DataObject
{
    /**
     * Skip parent constructor to avoid dependencies
     */
    public function __construct()
    {
        // Skip parent constructor
    }

    /**
     * Custom setIsAllowed method for testing
     *
     * @param bool $allowed
     * @return self
     */
    public function setIsAllowed($allowed): self
    {
        return $this;
    }

    /**
     * Custom getId method for testing
     *
     * @return int|null
     */
    public function getId()
    {
        return null;
    }

    /**
     * Custom getLinks method for testing
     *
     * @return mixed
     */
    public function getLinks()
    {
        return null;
    }

    /**
     * Override addData method
     *
     * @param array $data
     * @return self
     */
    public function addData($data): self
    {
        return $this;
    }
}
