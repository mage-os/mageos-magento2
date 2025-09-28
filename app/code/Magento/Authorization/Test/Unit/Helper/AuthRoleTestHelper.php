<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Authorization\Test\Unit\Helper;

use Magento\Authorization\Model\Role;

/**
 * Test helper for Magento\Authorization\Model\Role (AuthRole alias)
 */
class AuthRoleTestHelper extends Role
{
    private $gwsDataIsset = false;
    
    public function __construct()
    {
        // Skip parent constructor to avoid dependencies
    }
    
    /**
     * Load role
     * 
     * @param mixed $modelId
     * @param string $field
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
