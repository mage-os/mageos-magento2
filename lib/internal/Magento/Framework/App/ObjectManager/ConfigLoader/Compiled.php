<?php
/**
 * Copyright 2014 Adobe
 * All Rights Reserved.
 */
namespace Magento\Framework\App\ObjectManager\ConfigLoader;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\ObjectManager\Config\Compiled as CompiledConfig;
use Magento\Framework\ObjectManager\ConfigLoaderInterface;

/**
 * Load configuration files
 */
class Compiled implements ConfigLoaderInterface
{
    /**
     * Marks a compiled file that only holds the differences against the area it names
     *
     * Written by setup:di:compile for every non-global area. load() resolves it, so it never reaches callers.
     */
    public const EXTENDS_KEY = '_extends';

    /**
     * Global config
     *
     * @var array
     */
    private $configCache = [];

    /**
     * Returns the complete configuration of an area
     *
     * A compiled file marked with EXTENDS_KEY only holds the entries that differ from the area it names;
     * it is resolved against that area here, so callers always receive the complete configuration.
     * The resolved array is deliberately not cached: it would stay in process memory for the whole
     * request, whereas the contents of the compiled files live in OPcache shared memory.
     *
     * @param string $area
     * @return array
     * @throws \LogicException When a delta cannot be resolved against its base
     */
    public function load($area)
    {
        $diConfiguration = $this->loadFile($area);
        if (!is_array($diConfiguration) || !array_key_exists(self::EXTENDS_KEY, $diConfiguration)) {
            return $diConfiguration;
        }

        $base = $diConfiguration[self::EXTENDS_KEY];
        if (!is_string($base) || $base === '' || $base === $area) {
            throw new \LogicException(
                sprintf('The compiled DI configuration of "%s" extends an invalid area.', $area)
            );
        }

        $baseConfiguration = $this->loadFile($base);
        if (!is_array($baseConfiguration)) {
            throw new \LogicException(sprintf(
                'The compiled DI configuration of "%s" extends "%s", which could not be loaded.',
                $area,
                $base
            ));
        }
        if (array_key_exists(self::EXTENDS_KEY, $baseConfiguration)) {
            throw new \LogicException(sprintf(
                'The compiled DI configuration of "%s" extends "%s", which is itself a delta.',
                $area,
                $base
            ));
        }

        return self::merge($baseConfiguration, $diConfiguration);
    }

    /**
     * Returns the contents of a compiled configuration file as written by setup:di:compile
     *
     * @param string $area
     * @return array|mixed
     */
    protected function loadFile($area)
    {
        if (!isset($this->configCache[$area])) {
            $this->configCache[$area] = include self::getFilePath($area);
        }

        return $this->configCache[$area];
    }

    /**
     * Applies a delta on top of its base the same way ObjectManager\Config\Compiled::extend() does
     *
     * Merged sections are replaced per top-level key; any other entry of the delta replaces the base one.
     *
     * @param array $base
     * @param array $delta
     * @return array
     */
    private static function merge(array $base, array $delta)
    {
        unset($delta[self::EXTENDS_KEY]);

        foreach ($delta as $section => $values) {
            $base[$section] = in_array($section, CompiledConfig::MERGED_SECTIONS, true)
                && is_array($values)
                && is_array($base[$section] ?? null)
                    ? array_replace($base[$section], $values)
                    : $values;
        }

        return $base;
    }

    /**
     * Returns path to compiled configuration
     *
     * @param string $area
     * @return string
     */
    public static function getFilePath($area)
    {
        $diPath = DirectoryList::getDefaultConfig()[DirectoryList::GENERATED_METADATA][DirectoryList::PATH];
        return BP . '/' . $diPath . '/' . $area . '.php';
    }
}
