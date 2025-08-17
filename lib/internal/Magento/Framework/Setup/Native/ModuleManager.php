<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Setup\Native;

/**
 * Native ModuleManager that provides minimal module loading functionality for setup application
 */
class ModuleManager
{
    /**
     * @var array
     */
    private $modules;

    /**
     * @var \Laminas\EventManager\EventManagerInterface
     */
    private $eventManager;

    /**
     * Constructor
     *
     * @param array $modules
     * @param \Laminas\EventManager\EventManagerInterface $eventManager
     */
    public function __construct(array $modules, \Laminas\EventManager\EventManagerInterface $eventManager)
    {
        $this->modules = $modules;
        $this->eventManager = $eventManager;
    }

    /**
     * Load modules (loads configuration from each module)
     *
     * @return void
     */
    public function loadModules()
    {
        $config = [];
        
        // Load configuration from each module
        foreach ($this->modules as $moduleName) {
            if (class_exists($moduleName . '\Module')) {
                $moduleClass = $moduleName . '\Module';
                $moduleInstance = new $moduleClass();
                
                if (method_exists($moduleInstance, 'getConfig')) {
                    $moduleConfig = $moduleInstance->getConfig();
                    if (is_array($moduleConfig)) {
                        $config = array_merge_recursive($config, $moduleConfig);
                    }
                }
            }
        }
        
        // Trigger event to allow modules to be loaded
        $this->eventManager->trigger('loadModules.post', $this, ['config' => $config]);
    }
}
