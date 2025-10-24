<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Helper;

use Magento\Backend\Model\Auth\Session;
use Magento\Framework\DataObject;
use Magento\Framework\Session\Test\Unit\Helper\StorageTestHelper;
use Magento\User\Model\User;

/**
 * Test helper for creating Auth Session mocks with various methods
 *
 * This helper extends the concrete Auth\Session class directly, providing a clean
 * way to add test-specific methods without using anonymous classes.
 * Extends Auth\Session to be compatible with type hints requiring that specific class.
 */
class SessionTestHelper extends Session
{
    /**
     * @var User|null
     */
    private $user = null;
    
    /**
     * @var bool
     */
    private $isLoggedIn = false;
    
    /**
     * @var DataObject|null
     */
    private $sessionData = null;
    
    /**
     * @var mixed
     */
    private $skipLoggingAction = null;
    
    /**
     * @var callable|null
     */
    private $isLoggedInCallback = null;
    
    public function __construct(?DataObject $sessionData = null)
    {
        // Skip parent constructor for testing
        // Initialize storage with the StorageTestHelper
        $this->storage = new StorageTestHelper();
        $this->sessionData = $sessionData;
    }
    
    /**
     * Get user
     *
     * @return User|null
     */
    public function getUser()
    {
        return $this->user;
    }
    
    /**
     * Set user
     *
     * @param User $user
     * @return $this
     */
    public function setUser($user)
    {
        $this->user = $user;
        $this->isLoggedIn = true;
        return $this;
    }
    
    /**
     * Set data
     *
     * @param string|array $key
     * @param mixed $value
     * @return $this
     */
    public function setData($key, $value = null)
    {
        if ($this->sessionData) {
            $this->sessionData->setData($key, $value);
        }
        return $this;
    }
    
    /**
     * Get data
     *
     * @param string $key
     * @param mixed $index
     * @return mixed
     */
    public function getData($key = '', $index = null)
    {
        if ($this->sessionData) {
            return $this->sessionData->getData($key, $index);
        }
        return null;
    }
    
    /**
     * Get skip logging action
     *
     * @return mixed
     */
    public function getSkipLoggingAction()
    {
        return $this->skipLoggingAction;
    }
    
    /**
     * Set skip logging action
     *
     * @param mixed $value
     * @return $this
     */
    public function setSkipLoggingAction($value)
    {
        $this->skipLoggingAction = $value;
        return $this;
    }
    
    /**
     * Set is logged in callback
     *
     * @param callable $callback
     * @return $this
     */
    public function setIsLoggedInCallback($callback)
    {
        $this->isLoggedInCallback = $callback;
        return $this;
    }
    
    /**
     * Override isLoggedIn to use callback if set
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        if ($this->isLoggedInCallback) {
            return call_user_func($this->isLoggedInCallback);
        }
        return $this->isLoggedIn;
    }
    
    /**
     * Set is logged in
     *
     * @param bool $value
     * @return $this
     */
    public function setIsLoggedIn($value)
    {
        $this->isLoggedIn = $value;
        return $this;
    }

    /**
     * Set URL notice flag
     *
     * @param bool $flag
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setIsUrlNotice($flag)
    {
        return $this;
    }
}
