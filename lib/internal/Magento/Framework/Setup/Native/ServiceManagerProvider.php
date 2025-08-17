<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Setup\Native;

/**
 * Provider that gives access to the native ServiceManager instance
 * This bridges the gap between native ServiceManager and Magento's ObjectManager
 */
class ServiceManagerProvider
{
    /**
     * @var ServiceManager
     */
    private static $serviceManager;

    /**
     * Set the service manager instance
     *
     * @param ServiceManager $serviceManager
     * @return void
     */
    public static function setServiceManager(ServiceManager $serviceManager)
    {
        self::$serviceManager = $serviceManager;
    }

    /**
     * Get the service manager instance
     *
     * @return ServiceManager
     * @throws \Exception
     */
    public static function getServiceManager()
    {
        if (!self::$serviceManager) {
            throw new \Exception('ServiceManager not initialized in ServiceManagerProvider');
        }
        return self::$serviceManager;
    }
}
