<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Setup\Native;

/**
 * Native ServiceLocator interface that provides compatibility with Laminas\ServiceManager\ServiceLocatorInterface
 * This allows ObjectManagerProvider to work with native implementation
 */
interface ServiceLocatorInterface
{
    /**
     * Retrieve a service
     *
     * @param string $name
     * @return mixed
     */
    public function get($name);

    /**
     * Check if a service exists
     *
     * @param string $name
     * @return bool
     */
    public function has($name);
}
