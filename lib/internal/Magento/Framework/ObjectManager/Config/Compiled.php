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
     * Sections of a compiled configuration that are merged per top-level key
     */
    public const MERGED_SECTIONS = ['arguments', 'instanceTypes', 'preferences', 'lazyTypes'];

    /**
     * @var array
     */
    private $arguments;

    /**
     * @var array
     */
    private $instanceTypes;

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
     * Area whose complete configuration the current state is known to hold. Only the global
     * bootstrap marks it; any later extension clears it, so the next delta rebuilds from its base.
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
        foreach (self::MERGED_SECTIONS as $section) {
            $this->{$section} = isset($data[$section]) && is_array($data[$section]) ? $data[$section] : [];
        }
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
        if (isset($this->instanceTypes[$instanceName])) {
            return $this->instanceTypes[$instanceName];
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

        foreach (self::MERGED_SECTIONS as $section) {
            if (isset($configuration[$section]) && is_array($configuration[$section])) {
                $this->{$section} = array_replace($this->{$section}, $configuration[$section]);
            }
        }
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
        foreach (self::MERGED_SECTIONS as $section) {
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
        return $this->instanceTypes;
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
