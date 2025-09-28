<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\User\Test\Unit\Helper;

use Magento\User\Model\User;

/**
 * Test helper for Magento\User\Model\User
 */
class UserTestHelper extends User
{
    private $role = null;
    
    public function __construct()
    {
        // Skip parent constructor to avoid dependencies
    }
    
    /**
     * Set role
     * 
     * @param mixed $role
     * @return $this
     */
    public function setRole($role)
    {
        $this->role = $role;
        return $this;
    }
    
    /**
     * Get role
     * 
     * @return mixed
     */
    public function getRole()
    {
        return $this->role;
    }
}
