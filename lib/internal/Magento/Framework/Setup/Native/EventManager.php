<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Setup\Native;

/**
 * Native EventManager that provides minimal EventManager functionality for setup application
 * This is a simplified implementation that provides only what the setup application needs
 */
class EventManager implements EventManagerInterface
{
    /**
     * @var SharedEventManagerInterface
     */
    private $sharedManager;

    /**
     * Constructor
     *
     * @param SharedEventManagerInterface|null $sharedManager
     */
    public function __construct(?SharedEventManagerInterface $sharedManager = null)
    {
        $this->sharedManager = $sharedManager;
    }

    /**
     * Get shared manager
     *
     * @return SharedEventManagerInterface|null
     */
    public function getSharedManager()
    {
        return $this->sharedManager;
    }

    /**
     * Set shared manager
     *
     * @param SharedEventManagerInterface $sharedManager
     * @return self
     */
    public function setSharedManager(SharedEventManagerInterface $sharedManager)
    {
        $this->sharedManager = $sharedManager;
        return $this;
    }

    // Minimal implementation - setup application doesn't use most EventManager features
    // These methods exist for interface compliance but don't need full implementation
    
    public function trigger($eventName, $target = null, $argv = []) 
    {
        // Minimal implementation for setup application
        return new EventResult();
    }

    public function attach($eventName, callable $listener, $priority = 1) 
    {
        // Minimal implementation for setup application
        return $this;
    }

    public function detach($listener) 
    {
        // Minimal implementation for setup application
        return $this;
    }
}

/**
 * Minimal EventManagerInterface for setup application
 */
interface EventManagerInterface
{
    public function getSharedManager();
    public function setSharedManager(SharedEventManagerInterface $sharedManager);
    public function trigger($eventName, $target = null, $argv = []);
    public function attach($eventName, callable $listener, $priority = 1);
    public function detach($listener);
}

/**
 * Minimal EventResult for setup application
 */
class EventResult
{
    public function stopped()
    {
        return false;
    }
}
