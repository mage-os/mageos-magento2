<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Setup;

use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Proxy to provide the Laminas ServiceManager instance to Magento's ObjectManager
 * This bridges the gap between the setup bootstrapping (Laminas ServiceManager) 
 * and command execution (Magento ObjectManager)
 */
class ServiceManagerFactory implements ServiceLocatorInterface
{
    /**
     * @var ServiceLocatorInterface|null
     */
    private static $serviceManager = null;

    /**
     * Set the ServiceManager instance (called during setup bootstrap)
     *
     * @param ServiceLocatorInterface $serviceManager
     */
    public static function setServiceManager(ServiceLocatorInterface $serviceManager)
    {
        self::$serviceManager = $serviceManager;
    }

    /**
     * Get a service from the ServiceManager
     *
     * @param string $name
     * @return mixed
     * @throws \RuntimeException
     */
    public function get($name)
    {
        if (self::$serviceManager === null) {
            throw new \RuntimeException('ServiceManager not initialized. This should be set during setup bootstrap.');
        }

        return self::$serviceManager->get($name);
    }

    /**
     * Check if a service exists in the ServiceManager
     *
     * @param string $name
     * @return bool
     * @throws \RuntimeException
     */
    public function has($name)
    {
        if (self::$serviceManager === null) {
            throw new \RuntimeException('ServiceManager not initialized. This should be set during setup bootstrap.');
        }

        return self::$serviceManager->has($name);
    }

    /**
     * Build a service by its name, using optional options
     *
     * @param string $name
     * @param array|null $options
     * @return mixed
     * @throws \RuntimeException
     */
    public function build($name, ?array $options = null)
    {
        if (self::$serviceManager === null) {
            throw new \RuntimeException('ServiceManager not initialized. This should be set during setup bootstrap.');
        }

        return self::$serviceManager->build($name, $options);
    }
}
