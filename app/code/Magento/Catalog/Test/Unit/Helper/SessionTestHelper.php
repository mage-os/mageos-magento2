<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Backend\Model\Auth\Session;
use Magento\User\Model\User;

/**
 * Test helper for Backend Auth Session
 *
 * Extends Session to add custom methods for testing
 */
class SessionTestHelper extends Session
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * Constructor - skip parent to avoid dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get user for testing
     *
     * @return User|null
     */
    public function getUser()
    {
        return $this->data['user'] ?? null;
    }

    /**
     * Set user for testing
     *
     * @param User $user
     * @return $this
     */
    public function setUser($user)
    {
        $this->data['user'] = $user;
        return $this;
    }
}
