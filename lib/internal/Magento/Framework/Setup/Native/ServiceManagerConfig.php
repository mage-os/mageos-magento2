<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Setup\Native;

/**
 * Native ServiceManagerConfig that replicates Laminas\Mvc\Service\ServiceManagerConfig functionality
 * This provides identical behavior for configuring the ServiceManager without Laminas dependencies
 */
class ServiceManagerConfig
{
    /**
     * @var array
     */
    protected $config = [
        'abstract_factories' => [],
        'aliases' => [
            // Core aliases that setup application expects
            'EventManagerInterface' => 'EventManager',
            'ServiceLocatorInterface' => ServiceManager::class,
        ],
        'delegators' => [],
        'factories' => [
            // Core factories that setup application expects
            'EventManager' => EventManagerFactory::class,
            'SharedEventManager' => SharedEventManagerFactory::class,
            'ModuleManager' => ModuleManagerFactory::class,
            'ServiceListener' => ServiceListenerFactory::class,
            \Magento\Setup\Model\ObjectManagerProvider::class => ObjectManagerProviderFactory::class,
            // Setup commands should use MagentoDiFactory for full DI resolution
            \Magento\Setup\Console\Command\DiCompileCommand::class => \Magento\Setup\Di\MagentoDiFactory::class,
        ],
        'lazy_services' => [],
        'initializers' => [],
        'invokables' => [],
        'services' => [],
        'shared' => [
            'EventManager' => false, // Same as Laminas - EventManager is not shared
        ],
    ];

    /**
     * Constructor - replicates Laminas ServiceManagerConfig behavior
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        // Add ServiceManager factory (same as Laminas)
        $this->config['factories']['ServiceManager'] = function($container) {
            return $container;
        };

        // Add SharedEventManager factory (same as Laminas)
        $this->config['factories']['SharedEventManager'] = function() {
            return new SharedEventManager();
        };

        // Add EventManagerAware initializer (same as Laminas)
        $this->config['initializers']['EventManagerAwareInitializer'] = function($container, $instance) {
            if (!$instance instanceof EventManagerAwareInterface) {
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

        // Merge with provided config
        if (!empty($config)) {
            $this->config = array_merge_recursive($this->config, $config);
        }
    }

    /**
     * Configure service manager (same API as Laminas)
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
        
        // Configure the service manager
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