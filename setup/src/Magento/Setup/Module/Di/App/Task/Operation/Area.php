<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
namespace Magento\Setup\Module\Di\App\Task\Operation;

use Magento\Setup\Module\Di\App\Task\OperationInterface;
use Magento\Framework\App;
use Magento\Framework\ObjectManager\Config\Compiled as CompiledConfig;
use Magento\Framework\ObjectManager\ConfigLoaderInterface;
use Magento\Setup\Module\Di\Compiler\Config;
use Magento\Setup\Module\Di\Definition\Collection as DefinitionsCollection;

/**
 * Area configuration aggregation
 */
class Area implements OperationInterface
{
    /**
     * @var App\AreaList
     */
    private $areaList;

    /**
     * @var \Magento\Setup\Module\Di\Code\Reader\Decorator\Area
     */
    private $areaInstancesNamesList;

    /**
     * @var Config\Reader
     */
    private $configReader;

    /**
     * @var \Magento\Framework\App\ObjectManager\ConfigWriterInterface
     */
    private $configWriter;

    /**
     * @var array
     */
    private $data = [];

    /**
     * @var \Magento\Setup\Module\Di\Compiler\Config\ModificationChain
     */
    private $modificationChain;

    /**
     * @param App\AreaList $areaList
     * @param \Magento\Setup\Module\Di\Code\Reader\Decorator\Area $areaInstancesNamesList
     * @param Config\Reader $configReader
     * @param \Magento\Framework\App\ObjectManager\ConfigWriterInterface $configWriter
     * @param \Magento\Setup\Module\Di\Compiler\Config\ModificationChain $modificationChain
     * @param array $data
     */
    public function __construct(
        App\AreaList $areaList,
        \Magento\Setup\Module\Di\Code\Reader\Decorator\Area $areaInstancesNamesList,
        Config\Reader $configReader,
        \Magento\Framework\App\ObjectManager\ConfigWriterInterface $configWriter,
        Config\ModificationChain $modificationChain,
        $data = []
    ) {
        $this->areaList = $areaList;
        $this->areaInstancesNamesList = $areaInstancesNamesList;
        $this->configReader = $configReader;
        $this->configWriter = $configWriter;
        $this->data = $data;
        $this->modificationChain = $modificationChain;
    }

    /**
     * @inheritdoc
     */
    public function doOperation()
    {
        if (empty($this->data)) {
            return;
        }

        $definitionsCollection = new DefinitionsCollection();
        foreach ($this->data as $paths) {
            if (!is_array($paths)) {
                $paths = (array)$paths;
            }
            foreach ($paths as $path) {
                $definitionsCollection->addCollection($this->getDefinitionsCollection($path));
            }
        }

        $this->sortDefinitions($definitionsCollection);

        $areaCodes = array_merge([App\Area::AREA_GLOBAL], $this->areaList->getCodes());
        $globalConfig = [];
        foreach ($areaCodes as $areaCode) {
            $config = $this->configReader->generateCachePerScope($definitionsCollection, $areaCode);
            $config = $this->modificationChain->modify($config);

            // sort configuration to have it in the same order on every build
            ksort($config['arguments']);
            ksort($config['preferences']);
            ksort($config['instanceTypes']);

            if ($areaCode === App\Area::AREA_GLOBAL) {
                $globalConfig = $config;
            } else {
                $config = $this->extractDiff($config, $globalConfig);
            }

            $this->configWriter->write($areaCode, $config);
        }
    }

    /**
     * Reduces an area configuration to the entries that differ from the global one
     *
     * @param array $config
     * @param array $globalConfig
     * @return array
     */
    private function extractDiff(array $config, array $globalConfig)
    {
        $diff = [ConfigLoaderInterface::EXTENDS_KEY => App\Area::AREA_GLOBAL];

        foreach ($config as $section => $values) {
            if (!is_array($values) || !in_array($section, CompiledConfig::MERGED_SECTIONS, true)) {
                $diff[$section] = $values;
                continue;
            }

            $globalValues = $globalConfig[$section] ?? [];
            $sectionDiff = [];
            foreach ($values as $key => $value) {
                if (!array_key_exists($key, $globalValues) || $globalValues[$key] !== $value) {
                    $sectionDiff[$key] = $value;
                }
            }
            $diff[$section] = $sectionDiff;
        }

        return $diff;
    }

    /**
     * Returns definitions collection
     *
     * @param string $path
     * @return DefinitionsCollection
     */
    protected function getDefinitionsCollection($path)
    {
        $definitions = new DefinitionsCollection();
        foreach ($this->areaInstancesNamesList->getList($path) as $className => $constructorArguments) {
            $definitions->addDefinition($className, $constructorArguments);
        }
        return $definitions;
    }

    /**
     * Returns operation name
     *
     * @return string
     */
    public function getName()
    {
        return 'Area configuration aggregation';
    }

    /**
     * Sort definitions to make reproducible result
     *
     * @param DefinitionsCollection $definitionsCollection
     */
    private function sortDefinitions(DefinitionsCollection $definitionsCollection): void
    {
        $definitions = $definitionsCollection->getCollection();

        ksort($definitions);

        $definitionsCollection->initialize($definitions);
    }
}
