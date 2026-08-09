<?php
/**
 * Copyright 2014 Adobe
 * All Rights Reserved.
 */
namespace Magento\Framework\ObjectManager\Config;

use Magento\Framework\ObjectManager\ConfigInterface;
use Magento\Framework\ObjectManager\ConfigCacheInterface;
use Magento\Framework\ObjectManager\ConfigLoaderInterface;
use Magento\Framework\ObjectManager\LazyTypeAwareInterface;
use Magento\Framework\ObjectManager\RelationsInterface;

/**
 * Provides object manager configuration when in compiled mode
 */
class Compiled implements ConfigInterface, LazyTypeAwareInterface
{
    /**
     * @var array
     */
    private $arguments;

    /**
     * @var array
     */
    private $virtualTypes;

    /**
     * @var array
     */
    private $preferences;

    /**
     * Compile-time allow-list of types provably eligible for PHP 8.4 lazy ghosts.
     *
     * @var array<string,bool>
     */
    private array $lazyTypes = [];

    /**
     * Area whose configuration is currently applied, null when the state matches no single area
     *
     * @var string|null
     */
    private $appliedArea;

    /**
     * @var ConfigLoaderInterface|null
     */
    private $configLoader;

    /**
     * @param array $data
     * @param ConfigLoaderInterface|null $configLoader
     */
    public function __construct($data, ?ConfigLoaderInterface $configLoader = null)
    {
        $this->configLoader = $configLoader;
        $this->appliedArea = $data[ConfigLoaderInterface::AREA_KEY] ?? null;
        $this->arguments = isset($data['arguments']) && is_array($data['arguments'])
            ? $data['arguments'] : [];
        $this->virtualTypes = isset($data['instanceTypes']) && is_array($data['instanceTypes'])
            ? $data['instanceTypes'] : [];
        $this->preferences = isset($data['preferences']) && is_array($data['preferences'])
            ? $data['preferences'] : [];
        $this->lazyTypes = isset($data['lazyTypes']) && is_array($data['lazyTypes'])
            ? $data['lazyTypes'] : [];
    }

    /**
     * Whether the given concrete type is absent from the compile-time lazy-eligibility allow-list.
     *
     * Opt-in semantics: only types the compile-time scan proved compatible with PHP 8.4 lazy
     * ghosts are lazy; anything unknown (including transitive auto-wired dependencies never
     * seen at compile time) is non-lazy. Fails safe: with no compile-time data present the
     * allow-list is empty and every type is non-lazy.
     *
     * @param string $type
     * @return bool
     */
    public function isNonLazyType(string $type): bool
    {
        return !isset($this->lazyTypes[$type]);
    }

    /**
     * Set class relations
     *
     * @param RelationsInterface $relations
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * phpcs:disable Magento2.CodeAnalysis.EmptyBlock
     */
    public function setRelations(RelationsInterface $relations)
    {
    }

    /**
     * Set configuration cache instance
     *
     * @param ConfigCacheInterface $cache
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setCache(ConfigCacheInterface $cache)
    {
    }
    // phpcs:enable Magento2.CodeAnalysis.EmptyBlock

    /**
     * Retrieve list of arguments per type
     *
     * @param string $type
     * @return array
     */
    public function getArguments($type)
    {
        if (array_key_exists($type, $this->arguments)) {
            if ($this->arguments[$type] === null) {
                $this->arguments[$type] = [];
            }
            return $this->arguments[$type];
        } else {
            return null;
        }
    }

    /**
     * Check whether type is shared
     *
     * @param string $type
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function isShared($type)
    {
        return true;
    }

    /**
     * Retrieve instance type
     *
     * @param string $instanceName
     * @return mixed
     */
    public function getInstanceType($instanceName)
    {
        if (isset($this->virtualTypes[$instanceName])) {
            return $this->virtualTypes[$instanceName];
        }
        return $instanceName;
    }

    /**
     * Retrieve preference for type
     *
     * @param string $type
     * @return string
     * @throws \LogicException
     */
    public function getPreference($type)
    {
        $type = $type !== null ? ltrim($type, '\\') : '';
        if (isset($this->preferences[$type])) {
            return $this->preferences[$type];
        }
        return $type;
    }

    /**
     * Extend configuration
     *
     * @param array $configuration
     * @return void
     */
    public function extend(array $configuration)
    {
        $configuration = $this->resolveAgainstAppliedArea($configuration);

        $this->arguments = isset($configuration['arguments']) && is_array($configuration['arguments'])
            ? array_replace($this->arguments, $configuration['arguments'])
            : $this->arguments;
        $this->virtualTypes = isset($configuration['instanceTypes']) && is_array($configuration['instanceTypes'])
            ? array_replace($this->virtualTypes, $configuration['instanceTypes'])
            : $this->virtualTypes;
        $this->preferences = isset($configuration['preferences']) && is_array($configuration['preferences'])
            ? array_replace($this->preferences, $configuration['preferences'])
            : $this->preferences;
        $this->lazyTypes = isset($configuration['lazyTypes']) && is_array($configuration['lazyTypes'])
            ? array_replace($this->lazyTypes, $configuration['lazyTypes'])
            : $this->lazyTypes;
    }

    /**
     * Returns a configuration that is equivalent to the complete one on top of the current state
     *
     * @param array $configuration
     * @return array
     */
    private function resolveAgainstAppliedArea(array $configuration)
    {
        $base = $configuration[ConfigLoaderInterface::EXTENDS_KEY] ?? null;
        $resolved = $base !== null && $base !== $this->appliedArea && $this->configLoader !== null
            ? self::merge($this->configLoader->load($base), $configuration)
            : $configuration;

        $this->appliedArea = $configuration[ConfigLoaderInterface::AREA_KEY] ?? null;

        return $resolved;
    }

    /**
     * Applies a configuration on top of another one, per top-level key of each section
     *
     * @param array $base
     * @param array $configuration
     * @return array
     */
    private static function merge(array $base, array $configuration)
    {
        foreach (['arguments', 'instanceTypes', 'preferences', 'lazyTypes'] as $section) {
            if (isset($configuration[$section]) && is_array($configuration[$section])) {
                $base[$section] = array_replace($base[$section] ?? [], $configuration[$section]);
            }
        }

        return $base;
    }

    /**
     * Retrieve all virtual types
     *
     * @return string
     */
    public function getVirtualTypes()
    {
        return $this->virtualTypes;
    }

    /**
     * Returns list on preferences
     *
     * @return array
     */
    public function getPreferences()
    {
        return $this->preferences;
    }
}
