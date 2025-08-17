<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Setup\Native;

/**
 * Native ModuleManagerFactory that creates ModuleManager for setup application
 */
class ModuleManagerFactory
{
    /**
     * Create ModuleManager instance (minimal implementation for setup)
     *
     * @param mixed $container Laminas ServiceManager
     * @param string $name
     * @param array|null $options
     * @return ModuleManager
     */
    public function __invoke($container, $name, ?array $options = null)
    {
        $configuration = $container->get('ApplicationConfig');
        $modules = isset($configuration['modules']) ? $configuration['modules'] : [];
        $eventManager = $container->get('EventManager');

        return new ModuleManager($modules, $eventManager);
    }
}
