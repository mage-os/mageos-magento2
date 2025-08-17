<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Setup\Native;

/**
 * Proxy class that provides ServiceManager instance to Magento's ObjectManager
 * This class can be instantiated by ObjectManager and provides access to the native ServiceManager
 */
class ServiceManagerProxy implements ServiceLocatorInterface
{
    /**
     * @var ServiceManager
     */
    private $serviceManager;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->serviceManager = ServiceManagerProvider::getServiceManager();
    }

    /**
     * Get service
     *
     * @param string $name
     * @return mixed
     */
    public function get($name)
    {
        return $this->serviceManager->get($name);
    }

    /**
     * Check if service exists
     *
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return $this->serviceManager->has($name);
    }
}
