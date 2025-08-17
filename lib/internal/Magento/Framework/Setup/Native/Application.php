<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Setup\Native;

/**
 * Native Application that replicates Laminas\Mvc\Application functionality for setup commands
 */
class Application
{
    /**
     * @var ServiceManager
     */
    private $serviceManager;

    /**
     * @var EventManagerInterface
     */
    private $eventManager;

    /**
     * @var mixed
     */
    private $request;

    /**
     * @var mixed
     */
    private $response;

    /**
     * Constructor (same signature as Laminas\Mvc\Application)
     *
     * @param ServiceManager $serviceManager
     * @param EventManagerInterface $eventManager
     * @param mixed $request
     * @param mixed $response
     */
    public function __construct(
        ServiceManager $serviceManager,
        EventManagerInterface $eventManager,
        $request = null,
        $response = null
    ) {
        $this->serviceManager = $serviceManager;
        $this->eventManager = $eventManager;
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * Get service manager (same API as Laminas)
     *
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * Get event manager (same API as Laminas)
     *
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        return $this->eventManager;
    }

    /**
     * Get request (same API as Laminas)
     *
     * @return mixed
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Get response (same API as Laminas)
     *
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Bootstrap the application (same API as Laminas)
     *
     * @param array $listeners
     * @return self
     */
    public function bootstrap(array $listeners = [])
    {
        // Bootstrap listeners (same behavior as Laminas MVC)
        foreach ($listeners as $listener) {
            if (is_string($listener)) {
                $listenerInstance = $this->serviceManager->get($listener);
                if (method_exists($listenerInstance, 'onBootstrap')) {
                    // Create a mock MvcEvent for compatibility
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
        $serviceManagerConfig = isset($configuration['service_manager']) ? $configuration['service_manager'] : [];
        $smConfig = new ServiceManagerConfig($serviceManagerConfig);

        $serviceManager = new ServiceManager();
        $smConfig->configureServiceManager($serviceManager);
        $serviceManager->setService('ApplicationConfig', $configuration);

        // Register ServiceManager instance with provider for ObjectManager access
        ServiceManagerProvider::setServiceManager($serviceManager);

        // Load modules and set up config
        $moduleManager = $serviceManager->get('ModuleManager');
        $moduleManager->loadModules();

        // Load autoload configurations (same as Laminas MVC does)
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

        // Load Setup module configuration and merge with application config
        if (class_exists('Magento\Setup\Module')) {
            $module = new \Magento\Setup\Module();
            $moduleConfig = $module->getConfig();
            $mergedConfig = array_merge_recursive($mergedConfig, $moduleConfig);
        }
        
        $serviceManager->setService('config', $mergedConfig);

        // Create application instance
        $eventManager = $serviceManager->get('EventManager');
        $request = $serviceManager->has('Request') ? $serviceManager->get('Request') : null;
        $response = $serviceManager->has('Response') ? $serviceManager->get('Response') : null;

        return new self($serviceManager, $eventManager, $request, $response);
    }
}