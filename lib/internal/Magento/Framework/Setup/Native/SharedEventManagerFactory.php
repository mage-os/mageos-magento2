<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Setup\Native;

/**
 * Native SharedEventManagerFactory
 */
class SharedEventManagerFactory
{
    /**
     * Create a SharedEventManager instance
     *
     * @param mixed $container Laminas ServiceManager
     * @param string $name
     * @param array|null $options
     * @return SharedEventManager
     */
    public function __invoke($container, $name, ?array $options = null)
    {
        return new SharedEventManager();
    }
}
