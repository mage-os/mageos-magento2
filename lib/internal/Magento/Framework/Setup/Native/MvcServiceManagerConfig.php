<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Setup\Native;

use Laminas\ServiceManager\ServiceManager;
use Laminas\EventManager\EventManager;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\SharedEventManager;
use Laminas\EventManager\SharedEventManagerInterface;
use Laminas\ModuleManager\ModuleManager;
use Laminas\ModuleManager\Listener\ServiceListener;

/**
 * Native ServiceManagerConfig that replicates Laminas\Mvc\Service\ServiceManagerConfig
 * but without MVC dependencies - only the core service management functionality
 */
class MvcServiceManagerConfig
{
    /**
     * @var array
     */
    protected $config;

    /**
     * Constructor
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        // Core configuration exactly matching original Laminas\Mvc\Service\ServiceManagerConfig
        $this->config = [
            'abstract_factories' => [],
            'aliases' => [
                'EventManagerInterface'            => EventManager::class,
                EventManagerInterface::class       => 'EventManager',
                ModuleManager::class               => 'ModuleManager',
                ServiceListener::class             => 'ServiceListener',
                SharedEventManager::class          => 'SharedEventManager',
                'SharedEventManagerInterface'      => 'SharedEventManager',
                SharedEventManagerInterface::class => 'SharedEventManager',
            ],
            'delegators' => [],
            'factories' => [
                'EventManager'            => function($container) {
                    $sharedManager = $container->has('SharedEventManager') ? $container->get('SharedEventManager') : null;
                    return new \Laminas\EventManager\EventManager($sharedManager);
                },
                'ModuleManager'           => ModuleManagerFactory::class,
                'ServiceListener'         => ServiceListenerFactory::class,
            ],
            'lazy_services' => [],
            'initializers' => [],
            'invokables' => [],
            'services' => [],
            'shared' => [
                'EventManager' => false,
            ],
        ];

        // Add ServiceManager factory (same as original)
        $this->config['factories']['ServiceManager'] = function($container) {
            return $container;
        };

        // Add SharedEventManager factory (same as original)
        $this->config['factories']['SharedEventManager'] = function() {
            return new \Laminas\EventManager\SharedEventManager();
        };

        // Add EventManagerAware initializer (same as original)
        $this->config['initializers']['EventManagerAwareInitializer'] = function($container, $instance) {
            if (!$instance instanceof \Laminas\EventManager\EventManagerAwareInterface) {
                return;
            }
            $eventManager = $instance->getEventManager();
            // If the instance has an EM WITH an SEM composed, do nothing.
            if ($eventManager instanceof EventManagerInterface
                && $eventManager->getSharedManager() instanceof SharedEventManagerInterface
            ) {
                return;
            }
            $instance->setEventManager($container->get('EventManager'));
        };

        // Merge with provided config (this includes application.config.php service_manager config)
        if (!empty($config)) {
            $this->config = array_merge_recursive($this->config, $config);
        }

        // Add InitParamListener as an invokable service (same as Laminas does)
        $this->config['invokables'][\Magento\Setup\Mvc\Bootstrap\InitParamListener::class] = \Magento\Setup\Mvc\Bootstrap\InitParamListener::class;
    }

    /**
     * Configure service manager (same API as Laminas\Mvc\Service\ServiceManagerConfig)
     *
     * @param ServiceManager $serviceManager
     * @return ServiceManager
     */
    public function configureServiceManager(ServiceManager $serviceManager)
    {
        // Add ServiceManager service reference (same as Laminas)
        $this->config['services'][ServiceManager::class] = $serviceManager;

        // Enable override during bootstrapping (same as Laminas)
        $serviceManager->setAllowOverride(true);
        
        // Configure the service manager using Laminas ServiceManager's native configure method
        $serviceManager->configure($this->config);
        
        // Disable override after configuration (same as Laminas)
        $serviceManager->setAllowOverride(false);

        return $serviceManager;
    }

    /**
     * Return all service configuration (same API as Laminas)
     *
     * @return array
     */
    public function toArray()
    {
        return $this->config;
    }
}
