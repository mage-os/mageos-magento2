<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Helper;

use Magento\Framework\ValidatorFactory;

/**
 * Test helper for Magento\Framework\ValidatorFactory
 */
class ValidatorFactoryTestHelper extends ValidatorFactory
{
    /**
     * @var string|null
     */
    private $instanceName;

    /**
     * Skip parent constructor
     */
    public function __construct()
    {
        // Skip parent constructor
    }

    /**
     * Set instance name (custom method for testing)
     *
     * @param string $instanceName
     * @return $this
     */
    public function setInstanceName($instanceName)
    {
        $this->instanceName = $instanceName;
        return $this;
    }

    /**
     * Get instance name
     *
     * @return string|null
     */
    public function getInstanceName()
    {
        return $this->instanceName;
    }
}


