<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Integration\Test\Unit\Helper;

use Magento\Integration\Model\Integration;

/**
 * Test helper for Integration
 *
 * This helper extends the concrete Integration class to provide
 * test-specific functionality without dependency injection issues.
 */
class IntegrationTestHelper extends Integration
{
    /**
     * @var string
     */
    private $name;

    /**
     * Constructor that skips parent initialization
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get name
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
}

