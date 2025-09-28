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
    /**
     * @var bool
     */
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
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function load($modelId, $field = null)
    {
        return $this;
    }
    
    /**
     * Is GWS data isset flag
     *
     * @return bool
     */
    public function isGwsDataIsset()
    {
        return $this->gwsDataIsset;
    }
    
    /**
     * Get GWS data isset flag (alias for compatibility)
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getGwsDataIsset()
    {
        return $this->isGwsDataIsset();
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
