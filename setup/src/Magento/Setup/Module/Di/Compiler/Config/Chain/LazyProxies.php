<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Setup\Module\Di\Compiler\Config\Chain;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Setup\Module\Di\Compiler\Config\ModificationInterface;

/**
 * Records concrete native lazy proxy targets while retaining generated proxies as the fallback.
 */
class LazyProxies implements ModificationInterface
{
    /**
     * @var DirectoryList
     */
    private readonly DirectoryList $directoryList;

    /**
     * @var File
     */
    private readonly File $fileDriver;

    /**
     * Resolve the configured generated-code location for proxy provenance checks.
     *
     * @param DirectoryList|null $directoryList
     * @param File|null $fileDriver
     */
    public function __construct(?DirectoryList $directoryList = null, ?File $fileDriver = null)
    {
        $this->directoryList = $directoryList ?? ObjectManager::getInstance()->get(DirectoryList::class);
        $this->fileDriver = $fileDriver ?? ObjectManager::getInstance()->get(File::class);
    }

    /**
     * Compute the native proxy allow-list from the resolved compiled configuration.
     *
     * @param array $config
     * @return array
     */
    public function modify(array $config): array
    {
        if (\PHP_VERSION_ID < 80400) {
            return $config;
        }

        $config['lazyProxies'] = [];
        $requiredProxies = $this->getRequiredProxyTypes($config);
        foreach ($this->getCandidates($config) as $proxyType) {
            if (isset($requiredProxies[$proxyType])) {
                continue;
            }
            $target = $this->getTarget($proxyType, $config);
            if ($target !== null) {
                $config['lazyProxies'][$proxyType] = $target;
            }
        }
        return $config;
    }

    /**
     * Collect type names from constructor entries, object references and type mappings.
     *
     * @param array $config
     * @return string[]
     */
    private function getCandidates(array $config): array
    {
        $arguments = $config['arguments'] ?? [];
        $candidates = array_fill_keys(array_keys($arguments), true);
        $this->collectArgumentTypes($arguments, $candidates);
        foreach (['instanceTypes', 'preferences'] as $section) {
            foreach ($config[$section] ?? [] as $type => $concrete) {
                $candidates[$type] = true;
                $candidates[$concrete] = true;
            }
        }
        return array_keys($candidates);
    }

    /**
     * Find shared and non-shared object references at any argument nesting depth.
     *
     * @param array $arguments
     * @param array $candidates
     * @return void
     */
    private function collectArgumentTypes(array $arguments, array &$candidates): void
    {
        foreach ($arguments as $key => $value) {
            if (in_array($key, ['_i_', '_ins_'], true) && is_string($value)) {
                $candidates[$value] = true;
            } elseif (is_array($value)) {
                $this->collectArgumentTypes($value, $candidates);
            }
        }
    }

    /**
     * Find generated subclasses that must remain available to satisfy constructor parameter types.
     *
     * @param array $config
     * @return array<string,bool>
     */
    private function getRequiredProxyTypes(array $config): array
    {
        $required = [];
        foreach (array_keys($config['arguments'] ?? []) as $consumer) {
            if (str_ends_with($consumer, '\\Proxy')) {
                continue;
            }
            try {
                $reflection = new \ReflectionClass($config['instanceTypes'][$consumer] ?? $consumer);
            } catch (\Throwable) {
                continue;
            }
            foreach ($reflection->getConstructor()?->getParameters() ?? [] as $parameter) {
                $this->collectRequiredProxyTypes($parameter->getType(), $required);
            }
        }
        return $required;
    }

    /**
     * Inspect named, nullable, union and intersection components of a constructor parameter.
     *
     * @param \ReflectionType|null $type
     * @param array $required
     * @return void
     */
    private function collectRequiredProxyTypes(?\ReflectionType $type, array &$required): void
    {
        if ($type instanceof \ReflectionNamedType) {
            if (str_ends_with($type->getName(), '\\Proxy')) {
                $required[$type->getName()] = true;
            }
        } elseif ($type instanceof \ReflectionUnionType || $type instanceof \ReflectionIntersectionType) {
            foreach ($type->getTypes() as $component) {
                $this->collectRequiredProxyTypes($component, $required);
            }
        }
    }

    /**
     * Resolve the same concrete class returned by the compiled object manager for the subject.
     *
     * @param string $proxyType
     * @param array $config
     * @return string|null
     */
    private function getTarget(string $proxyType, array $config): ?string
    {
        if (!str_ends_with($proxyType, '\\Proxy') || array_key_exists($proxyType, $config['instanceTypes'] ?? [])) {
            return null;
        }

        $subject = substr($proxyType, 0, -strlen('\\Proxy'));
        if (!$this->usesDefaultArguments($config['arguments'][$proxyType] ?? [], $subject)) {
            return null;
        }

        $preferred = $config['preferences'][$subject] ?? $subject;
        $target = $config['instanceTypes'][$preferred] ?? $preferred;
        try {
            $reflection = new \ReflectionClass($target);
        } catch (\Throwable) {
            return null;
        }

        return $this->isEligible($reflection) && $this->isGeneratedProxy($proxyType) ? $target : null;
    }

    /**
     * Preserve hand-written proxy implementations outside the configured generated-code directory.
     *
     * @param string $proxyType
     * @return bool
     */
    private function isGeneratedProxy(string $proxyType): bool
    {
        try {
            if (!class_exists($proxyType)) {
                return true;
            }
            $fileName = (new \ReflectionClass($proxyType))->getFileName();
        } catch (\Throwable) {
            return false;
        }
        if ($fileName === false) {
            return true;
        }

        $directory = $this->directoryList->getPath(DirectoryList::GENERATED_CODE);
        $directory = $this->fileDriver->getRealPath($directory) ?: $directory;
        $fileName = $this->fileDriver->getRealPath($fileName) ?: $fileName;
        return str_starts_with($fileName, rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR);
    }

    /**
     * Reject constructor overrides that cannot preserve generated-proxy resolution semantics.
     *
     * @param array $arguments
     * @param string $subject
     * @return bool
     */
    private function usesDefaultArguments(array $arguments, string $subject): bool
    {
        foreach (['instanceName' => $subject, 'shared' => true] as $name => $default) {
            if (!array_key_exists($name, $arguments)) {
                continue;
            }
            $argument = $arguments[$name];
            if ($name === 'instanceName' && isset($argument['_v_']) && is_string($argument['_v_'])) {
                $argument['_v_'] = ltrim($argument['_v_'], '\\');
            }
            if ($argument !== ['_v_' => $default]) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check PHP's target restrictions, including internal classes anywhere in the ancestry.
     *
     * @param \ReflectionClass $reflection
     * @return bool
     */
    private function isEligible(\ReflectionClass $reflection): bool
    {
        if ($reflection->isAbstract()
            || $reflection->isInterface()
            || $reflection->isTrait()
            || $reflection->isEnum()
            || $reflection->isReadOnly()
            || $reflection->hasMethod('_resetState')
        ) {
            return false;
        }
        for ($current = $reflection; $current; $current = $current->getParentClass()) {
            if ($current->isInternal()) {
                return false;
            }
        }
        return $this->hasInstanceProperties($reflection);
    }

    /**
     * Include private inherited state while rejecting classes with only static properties.
     *
     * @param \ReflectionClass $reflection
     * @return bool
     */
    private function hasInstanceProperties(\ReflectionClass $reflection): bool
    {
        for ($current = $reflection; $current; $current = $current->getParentClass()) {
            foreach ($current->getProperties() as $property) {
                if (!$property->isStatic()) {
                    return true;
                }
            }
        }
        return false;
    }
}
