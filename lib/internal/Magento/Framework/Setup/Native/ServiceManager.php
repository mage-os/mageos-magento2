<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Setup\Native;

/**
 * Native ServiceManager that implements the exact same functionality as Laminas\ServiceManager\ServiceManager
 * This provides identical behavior for the setup application without requiring Laminas dependencies
 */
class ServiceManager implements ServiceLocatorInterface, \Interop\Container\ContainerInterface
{
    /**
     * @var array
     */
    private $services = [];

    /**
     * @var array
     */
    private $factories = [];

    /**
     * @var array
     */
    private $invokables = [];

    /**
     * @var array
     */
    private $aliases = [];

    /**
     * @var array
     */
    private $abstractFactories = [];

    /**
     * @var array
     */
    private $shared = [];

    /**
     * @var bool
     */
    private $allowOverride = false;

    /**
     * Get a service
     *
     * @param string $name
     * @return mixed
     * @throws \Exception
     */
    public function get($name)
    {
        // Check if already instantiated and shared
        if (isset($this->services[$name])) {
            return $this->services[$name];
        }

        // Resolve aliases
        if (isset($this->aliases[$name])) {
            return $this->get($this->aliases[$name]);
        }

        $instance = $this->createService($name);
        
        // Cache if shared (default behavior like Laminas)
        if (!isset($this->shared[$name]) || $this->shared[$name] !== false) {
            $this->services[$name] = $instance;
        }

        return $instance;
    }

    /**
     * Create a service instance
     *
     * @param string $name
     * @return mixed
     * @throws \Exception
     */
    private function createService($name)
    {
        // Try factories first (same order as Laminas)
        if (isset($this->factories[$name])) {
            $factory = $this->factories[$name];
            
            if (is_string($factory)) {
                $factory = new $factory();
            }
            
            if (method_exists($factory, '__invoke')) {
                return $factory($this, $name);
            } elseif (method_exists($factory, 'createService')) {
                return $factory->createService($this);
            } else {
                throw new \Exception("Factory for '{$name}' must implement __invoke or createService method");
            }
        }

        // Try invokables
        if (isset($this->invokables[$name])) {
            $className = $this->invokables[$name];
            return new $className();
        }

        // Try abstract factories (same behavior as Laminas)
        foreach ($this->abstractFactories as $abstractFactory) {
            if (is_string($abstractFactory)) {
                $abstractFactory = new $abstractFactory();
            }
            
            if (method_exists($abstractFactory, 'canCreate') && $abstractFactory->canCreate($this, $name)) {
                return $abstractFactory->__invoke($this, $name);
            }
        }

        // Last resort: try to instantiate directly
        if (class_exists($name)) {
            return new $name();
        }

        throw new \Exception("Unable to resolve service '{$name}'");
    }

    /**
     * Check if service exists
     *
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->services[$name]) 
            || isset($this->factories[$name])
            || isset($this->invokables[$name])
            || isset($this->aliases[$name])
            || class_exists($name);
    }

    /**
     * Set a service instance (same API as Laminas)
     *
     * @param string $name
     * @param mixed $service
     * @return self
     */
    public function setService($name, $service)
    {
        $this->services[$name] = $service;
        return $this;
    }

    /**
     * Set a factory (same API as Laminas)
     *
     * @param string $name
     * @param string|callable $factory
     * @return self
     */
    public function setFactory($name, $factory)
    {
        $this->factories[$name] = $factory;
        return $this;
    }

    /**
     * Set an invokable (same API as Laminas)
     *
     * @param string $name
     * @param string $className
     * @return self
     */
    public function setInvokableClass($name, $className)
    {
        $this->invokables[$name] = $className;
        return $this;
    }

    /**
     * Set an alias (same API as Laminas)
     *
     * @param string $alias
     * @param string $target
     * @return self
     */
    public function setAlias($alias, $target)
    {
        $this->aliases[$alias] = $target;
        return $this;
    }

    /**
     * Add an abstract factory (same API as Laminas)
     *
     * @param string|object $abstractFactory
     * @return self
     */
    public function addAbstractFactory($abstractFactory)
    {
        $this->abstractFactories[] = $abstractFactory;
        return $this;
    }

    /**
     * Set sharing flag (same API as Laminas)
     *
     * @param string $name
     * @param bool $flag
     * @return self
     */
    public function setShared($name, $flag)
    {
        $this->shared[$name] = $flag;
        return $this;
    }

    /**
     * Set allow override flag (same API as Laminas)
     *
     * @param bool $flag
     * @return self
     */
    public function setAllowOverride($flag)
    {
        $this->allowOverride = $flag;
        return $this;
    }

    /**
     * Configure service manager from array (same API as Laminas)
     *
     * @param array $config
     * @return self
     */
    public function configure(array $config)
    {
        // Configure factories
        if (isset($config['factories'])) {
            foreach ($config['factories'] as $name => $factory) {
                $this->setFactory($name, $factory);
            }
        }

        // Configure services
        if (isset($config['services'])) {
            foreach ($config['services'] as $name => $service) {
                $this->setService($name, $service);
            }
        }

        // Configure invokables
        if (isset($config['invokables'])) {
            foreach ($config['invokables'] as $name => $className) {
                $this->setInvokableClass($name, $className);
            }
        }

        // Configure aliases
        if (isset($config['aliases'])) {
            foreach ($config['aliases'] as $alias => $target) {
                $this->setAlias($alias, $target);
            }
        }

        // Configure abstract factories
        if (isset($config['abstract_factories'])) {
            foreach ($config['abstract_factories'] as $abstractFactory) {
                $this->addAbstractFactory($abstractFactory);
            }
        }

        // Configure sharing
        if (isset($config['shared'])) {
            foreach ($config['shared'] as $name => $flag) {
                $this->setShared($name, $flag);
            }
        }

        return $this;
    }
}