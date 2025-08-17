<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Setup\Native;

use Laminas\ServiceManager\ServiceManager;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Stdlib\ArrayUtils;
use Magento\Framework\Setup\ServiceManagerFactory;

/**
 * Native MVC Application that replicates Laminas\Mvc\Application functionality
 * but without MVC routing/view dependencies - only the core bootstrapping needed for setup
 */
class MvcApplication
{
    /**
     * @var ServiceManager
     */
    private $serviceManager;

    /**
     * Constructor (compatible with Laminas\Mvc\Application)
     *
     * @param ServiceManager $serviceManager
     * @param mixed $eventManager
     * @param mixed $request
     * @param mixed $response
     */
    public function __construct(
        ServiceManager $serviceManager,
        $eventManager = null,
        $request = null,
        $response = null
    ) {
        $this->serviceManager = $serviceManager;
        // For setup commands, we don't need eventManager, request, response
    }

    /**
     * Get service manager (same API as Laminas\Mvc\Application)
     *
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * Bootstrap the application (same API as Laminas\Mvc\Application)
     *
     * @param array $listeners
     * @return self
     */
    public function bootstrap(array $listeners = [])
    {
        // Bootstrap listeners for setup
        foreach ($listeners as $listener) {
            if (is_string($listener)) {
                $listenerInstance = $this->serviceManager->get($listener);
                if (method_exists($listenerInstance, 'onBootstrap')) {
                    // Create a compatible MvcEvent
                    $event = new MvcEvent();
                    $event->setApplication($this);
                    $listenerInstance->onBootstrap($event);
                }
            }
        }

        return $this;
    }

    /**
     * Initialize application (static method same as Laminas\Mvc\Application::init)
     *
     * @param array $configuration
     * @return self
     */
    public static function init(array $configuration)
    {
        // Pre-load module configurations to get their service_manager configs
        $moduleConfigs = self::loadModuleConfigurations($configuration);
        
        // Merge application service_manager config with module service_manager configs
        $serviceManagerConfig = isset($configuration['service_manager']) ? $configuration['service_manager'] : [];
        foreach ($moduleConfigs as $moduleConfig) {
            if (isset($moduleConfig['service_manager'])) {
                $serviceManagerConfig = ArrayUtils::merge($serviceManagerConfig, $moduleConfig['service_manager']);
            }
        }
        
        $smConfig = new MvcServiceManagerConfig($serviceManagerConfig);

        $serviceManager = new ServiceManager();
        $smConfig->configureServiceManager($serviceManager);
        $serviceManager->setService('ApplicationConfig', $configuration);

        // Register ServiceManager instance for Magento's ObjectManager access
        ServiceManagerFactory::setServiceManager($serviceManager);

        // Load modules (now just for events, configs are already loaded)
        $moduleManager = $serviceManager->get('ModuleManager');
        $moduleManager->loadModules();

        // Load autoload configurations (global.php, local.php)
        self::loadAutoloadConfig($serviceManager, $configuration);

        return new self($serviceManager);
    }

    /**
     * Pre-load module configurations
     *
     * @param array $configuration
     * @return array
     */
    private static function loadModuleConfigurations(array $configuration)
    {
        $moduleConfigs = [];
        $modules = isset($configuration['modules']) ? $configuration['modules'] : [];
        
        // Load configuration from each module
        foreach ($modules as $moduleName) {
            if (class_exists($moduleName . '\Module')) {
                $moduleClass = $moduleName . '\Module';
                $moduleInstance = new $moduleClass();
                
                if (method_exists($moduleInstance, 'getConfig')) {
                    $moduleConfig = $moduleInstance->getConfig();
                    if (is_array($moduleConfig)) {
                        $moduleConfigs[] = $moduleConfig;
                    }
                }
            }
        }
        
        return $moduleConfigs;
    }

    /**
     * Load autoload configurations
     *
     * @param ServiceManager $serviceManager
     * @param array $configuration
     */
    private static function loadAutoloadConfig(ServiceManager $serviceManager, array $configuration)
    {
        $mergedConfig = $configuration;
        
        // Load global.php and local.php configurations from config_glob_paths
        if (isset($configuration['module_listener_options']['config_glob_paths'])) {
            foreach ($configuration['module_listener_options']['config_glob_paths'] as $globPath) {
                $files = glob($globPath, GLOB_BRACE);
                foreach ($files as $file) {
                    if (is_readable($file)) {
                        $autoloadConfig = include $file;
                        if (is_array($autoloadConfig)) {
                            $mergedConfig = array_merge_recursive($mergedConfig, $autoloadConfig);
                        }
                    }
                }
            }
        }

        // Load Setup module configuration
        if (class_exists('Magento\Setup\Module')) {
            $module = new \Magento\Setup\Module();
            $moduleConfig = $module->getConfig();
            $mergedConfig = array_merge_recursive($mergedConfig, $moduleConfig);
        }
        
        $serviceManager->setService('config', $mergedConfig);
    }
}
