<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Setup\Native;

/**
 * Native SharedEventManager that provides minimal functionality for setup application
 */
class SharedEventManager implements SharedEventManagerInterface
{
    /**
     * @var array
     */
    private $listeners = [];

    /**
     * Attach a listener to an event
     *
     * @param string|array $identifiers
     * @param string $eventName
     * @param callable $listener
     * @param int $priority
     * @return void
     */
    public function attach($identifiers, $eventName, callable $listener, $priority = 1)
    {
        if (!is_array($identifiers)) {
            $identifiers = [$identifiers];
        }

        foreach ($identifiers as $identifier) {
            if (!isset($this->listeners[$identifier])) {
                $this->listeners[$identifier] = [];
            }
            if (!isset($this->listeners[$identifier][$eventName])) {
                $this->listeners[$identifier][$eventName] = [];
            }
            $this->listeners[$identifier][$eventName][] = [
                'listener' => $listener,
                'priority' => $priority
            ];
        }
    }

    /**
     * Get listeners for an identifier and event
     *
     * @param array $identifiers
     * @param string $eventName
     * @return array
     */
    public function getListeners(array $identifiers, $eventName)
    {
        $listeners = [];
        foreach ($identifiers as $identifier) {
            if (isset($this->listeners[$identifier][$eventName])) {
                $listeners = array_merge($listeners, $this->listeners[$identifier][$eventName]);
            }
        }
        return $listeners;
    }

    /**
     * Detach a listener
     *
     * @param callable $listener
     * @param string|null $identifier
     * @param string|null $eventName
     * @return bool
     */
    public function detach(callable $listener, $identifier = null, $eventName = null)
    {
        // Minimal implementation for setup application
        return true;
    }
}

/**
 * Minimal SharedEventManagerInterface for setup application
 */
interface SharedEventManagerInterface
{
    public function attach($identifiers, $eventName, callable $listener, $priority = 1);
    public function getListeners(array $identifiers, $eventName);
    public function detach(callable $listener, $identifier = null, $eventName = null);
}

/**
 * Minimal EventManagerAwareInterface for setup application
 */
interface EventManagerAwareInterface
{
    public function setEventManager(EventManagerInterface $eventManager);
    public function getEventManager();
}
