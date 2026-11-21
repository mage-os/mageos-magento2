<?php
/**
 * Copyright 2014 Adobe
 * All Rights Reserved.
 */
namespace Magento\Framework\ObjectManager\Factory;

class Compiled extends AbstractFactory
{
    /**
     * Object manager config
     *
     * @var \Magento\Framework\ObjectManager\ConfigInterface
     */
    protected $config;

    /**
     * Global arguments list
     *
     * @var array
     */
    protected $globalArguments;

    /**
     * @var array
     */
    private $sharedInstances;

    /**
     * Whether the env.php kill-switch has disabled native lazy object construction.
     *
     * @var bool
     */
    private bool $lazyDisabled;

    /**
     * @param \Magento\Framework\ObjectManager\ConfigInterface $config
     * @param array $sharedInstances
     * @param array $globalArguments
     */
    public function __construct(
        \Magento\Framework\ObjectManager\ConfigInterface $config,
        &$sharedInstances = [],
        $globalArguments = []
    ) {
        $this->config = $config;
        $this->globalArguments = $globalArguments;
        $this->sharedInstances = &$sharedInstances;
        $this->lazyDisabled = !empty($globalArguments[
            \Magento\Framework\Config\ConfigOptionsListConstants::CONFIG_PATH_LAZY_OBJECT_LOADING_DISABLED
        ]);
    }

    /**
     * Create instance with call time arguments
     *
     * @param string $requestedType
     * @param array $arguments
     * @return object
     * @throws \Exception
     */
    public function create($requestedType, array $arguments = [])
    {
        $lazyProxy = $this->createLazyProxy($requestedType, $arguments);
        if ($lazyProxy !== null) {
            return $lazyProxy;
        }

        $type = $this->config->getInstanceType($requestedType);

        /**
         * On PHP 8.4, with no call-time overrides and a compile-time-eligible type,
         * defer construction via ReflectionClass::newLazyGhost(); the constructor
         * is invoked in-place when the ghost's state is first observed.
         */
        if (\PHP_VERSION_ID >= 80400
            && !$this->lazyDisabled
            && $arguments === []
            && $this->config instanceof \Magento\Framework\ObjectManager\LazyTypeAwareInterface
            && !$this->config->isNonLazyType($type)
        ) {
            $reflection = new \ReflectionClass($type);
            if ($reflection->getConstructor() !== null) {
                return $reflection->newLazyGhost(
                    function ($obj) use ($requestedType) {
                        $obj->__construct(...$this->resolveArguments($requestedType));
                    }
                );
            }
        }

        if ($this->config->getArguments($requestedType) === []) {
            return new $type();
        }

        return $this->createObject($type, $this->resolveArguments($requestedType, $arguments));
    }

    /**
     * Construct a native proxy only when compiled configuration proves the target eligible.
     *
     * @param string $requestedType
     * @param array $arguments
     * @return object|null
     */
    private function createLazyProxy(string $requestedType, array $arguments): ?object
    {
        if (!str_ends_with($requestedType, '\\Proxy')
            || \PHP_VERSION_ID < 80400
            || $this->lazyDisabled
            || $arguments !== []
            || !$this->config instanceof \Magento\Framework\ObjectManager\LazyProxyAwareInterface
        ) {
            return null;
        }

        $target = $this->config->getLazyProxyTarget($requestedType);
        if ($target === null) {
            return null;
        }

        $subject = substr($requestedType, 0, -strlen('\\Proxy'));
        if (!$this->resolvesToTarget($subject, $target)) {
            return null;
        }

        return (new \ReflectionClass($target))->newLazyProxy(function () use ($subject) {
            $real = $this->objectManager->get($subject);
            return (new \ReflectionClass($real))->initializeLazyObject($real);
        });
    }

    /**
     * Whether the subject still resolves at runtime to the class recorded at compile time.
     *
     * PHP rejects a real instance whose class differs from the native proxy class, while a generated proxy
     * accepts any subclass. Shared instances seeded at bootstrap (for example App\Filesystem\DirectoryList
     * registered for Framework\Filesystem\DirectoryList), added through addSharedInstance(), or preferences
     * changed through configure() can all diverge from the compiled target, so keep the generated proxy then.
     *
     * @param string $subject
     * @param string $target
     * @return bool
     */
    private function resolvesToTarget(string $subject, string $target): bool
    {
        $preferred = $this->config->getPreference($subject);
        if (isset($this->sharedInstances[$preferred])) {
            return get_class($this->sharedInstances[$preferred]) === $target;
        }

        return $this->config->getInstanceType($preferred) === $target;
    }

    /**
     * Resolve constructor arguments for a requested type.
     *
     * @param string $requestedType
     * @param array $arguments
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function resolveArguments($requestedType, array $arguments = []): array
    {
        $args = $this->config->getArguments($requestedType);

        if ($args === []) {
            return [];
        }

        if ($args === null) {
            // Arguments resolved at runtime (no pre-compiled DI cache entry).
            $type = $this->config->getInstanceType($requestedType);
            $parameters = $this->getDefinitions()->getParameters($type) ?: [];
            return $this->resolveArgumentsInRuntime($type, $parameters, $arguments);
        }

        /**
         * Case 2: arguments retrieved from pre-compiled DI cache
         *
         * Argument key meanings:
         *
         * _i_: shared instance of a class or interface
         * _ins_: non-shared instance of a class or interface
         * _v_: non-array literal value
         * _vac_: array, may be nested and contain other types of keys listed here (objects, array, nulls, etc)
         * _vn_: null value
         * _a_: value to be taken from named environment variable
         * _d_: default value in case environment variable specified by _a_ does not exist
         */
        foreach ($args as $key => &$argument) {
            if (isset($arguments[$key])) {
                $argument = $arguments[$key];
            } elseif (isset($argument['_i_'])) {
                $argument = $this->get($argument['_i_']);
            } elseif (isset($argument['_ins_'])) {
                $argument = $this->create($argument['_ins_']);
            } elseif (isset($argument['_v_'])) {
                $argument = $argument['_v_'];
            } elseif (isset($argument['_vac_'])) {
                $argument = $argument['_vac_'];
                $this->parseArray($argument);
            } elseif (isset($argument['_vn_'])) {
                $argument = null;
            } elseif (isset($argument['_a_'])) {
                if (isset($this->globalArguments[$argument['_a_']])) {
                    $argument = $this->globalArguments[$argument['_a_']];
                } else {
                    $argument = $argument['_d_'];
                }
            }
        }
        unset($argument);
        return $args;
    }

    /**
     * Parse array argument
     *
     * @param array $array
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function parseArray(&$array)
    {
        foreach ($array as $key => &$argument) {
            if ($argument === (array)$argument) {
                if (isset($argument['_i_'])) {
                    $argument = $this->get($argument['_i_']);
                } elseif (isset($argument['_ins_'])) {
                    $argument = $this->create($argument['_ins_']);
                } elseif (isset($argument['_a_'])) {
                    if (isset($this->globalArguments[$argument['_a_']])) {
                        $argument = $this->globalArguments[$argument['_a_']];
                    } else {
                        $argument = $argument['_d_'];
                    }
                } else {
                    $this->parseArray($argument);
                }
            }
        }
    }

    /**
     * Retrieve cached object instance
     *
     * @param string $type
     * @return mixed
     */
    protected function get($type)
    {
        if (!isset($this->sharedInstances[$type])) {
            $this->sharedInstances[$type] = $this->create($type);
        }
        return $this->sharedInstances[$type];
    }
}
