<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Setup\Native;

/**
 * Native ServiceListenerFactory that provides minimal ServiceListener for setup application
 */
class ServiceListenerFactory
{
    /**
     * Create ServiceListener instance (minimal implementation for setup)
     *
     * @param ServiceLocatorInterface $container
     * @param string $name
     * @param array|null $options
     * @return ServiceListener
     */
    public function __invoke(ServiceLocatorInterface $container, $name, ?array $options = null)
    {
        return new ServiceListener($container);
    }
}

/**
 * Minimal ServiceListener for setup application
 */
class ServiceListener
{
    /**
     * @var ServiceLocatorInterface
     */
    private $container;

    /**
     * Constructor
     *
     * @param ServiceLocatorInterface $container
     */
    public function __construct(ServiceLocatorInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Set default service config (minimal implementation)
     *
     * @param array $config
     * @return void
     */
    public function setDefaultServiceConfig(array $config)
    {
        // Minimal implementation for setup application
        // The setup application doesn't use complex service listener functionality
    }

    /**
     * Add service manager (minimal implementation)
     *
     * @param mixed $serviceManager
     * @param string $configKey
     * @param string $interface
     * @param string $method
     * @return void
     */
    public function addServiceManager($serviceManager, $configKey, $interface, $method)
    {
        // Minimal implementation for setup application
        // The setup application doesn't use complex service manager registration
    }

    /**
     * Attach to event manager (minimal implementation)
     *
     * @param EventManagerInterface $eventManager
     * @return void
     */
    public function attach(EventManagerInterface $eventManager)
    {
        // Minimal implementation for setup application
        // The setup application doesn't use complex event attachment
    }
}
