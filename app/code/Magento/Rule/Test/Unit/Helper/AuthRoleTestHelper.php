<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Authorization\Test\Unit\Helper;

use Magento\Authorization\Model\Role as AuthRole;

/**
 * Test helper for creating AuthRole mocks with GWS methods
 * 
 * This helper extends the concrete AuthRole class directly, providing a clean
 * way to add test-specific GWS methods without using anonymous classes.
 */
class AuthRoleTestHelper extends AuthRole
{
    /**
     * @var bool
     */
    private $gwsDataIsset = false;
    
    public function __construct()
    {
        // Skip parent constructor for testing
    }
    
    /**
     * Load method for testing
     * 
     * @param mixed $modelId
     * @param mixed $field
     * @return $this
     */
    public function load($modelId, $field = null)
    {
        return $this;
    }
    
    /**
     * Get GWS data isset flag
     * 
     * @return bool
     */
    public function getGwsDataIsset()
    {
        return $this->gwsDataIsset;
    }
    
    /**
     * Set GWS data isset flag
     * 
     * @param bool $value
     * @return $this
     */
    public function setGwsDataIsset($value)
    {
        $this->gwsDataIsset = $value;
        return $this;
    }
}
