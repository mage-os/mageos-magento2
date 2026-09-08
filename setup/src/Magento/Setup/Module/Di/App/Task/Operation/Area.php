<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
namespace Magento\Setup\Module\Di\App\Task\Operation;

use Magento\Setup\Module\Di\App\Task\OperationInterface;
use Magento\Setup\Module\Di\App\Task\Parallel;
use Magento\Framework\App;
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

        // Compilation has just deleted the cache directory, and the configuration cache recreates
        // it lazily the first time a non-global area is loaded. Workers must not race to create
        // it, so areas are processed here until one of them has been through that path; only the
        // remainder is handed to workers.
        while ($areaCodes) {
            $areaCode = array_shift($areaCodes);
            $this->configReader->applyThirdPartyInterfaces($definitionsCollection, $areaCode);
            $this->processArea($areaCode, $definitionsCollection);
            if ($areaCode !== App\Area::AREA_GLOBAL) {
                break;
            }
        }

        // Areas are not independent: generateCachePerScope() back-fills third-party preferences
        // into the shared collection, so each area sees the keys added by the areas before it.
        // The prepare step replays that back-fill in the original order in this process, so a
        // worker starts from exactly the collection its area would have had sequentially.
        Parallel::each(
            $areaCodes,
            function ($areaCode) use ($definitionsCollection) {
                $this->processArea($areaCode, $definitionsCollection);
            },
            function ($areaCode) use ($definitionsCollection) {
                $this->configReader->applyThirdPartyInterfaces($definitionsCollection, $areaCode);
            }
        );
    }

    /**
     * Build, modify and write the compiled DI configuration for a single area.
     *
     * @param string $areaCode
     * @param DefinitionsCollection $definitionsCollection
     * @return void
     */
    private function processArea($areaCode, DefinitionsCollection $definitionsCollection)
    {
        $config = $this->configReader->generateCachePerScope($definitionsCollection, $areaCode);
        $config = $this->modificationChain->modify($config);

        // sort configuration to have it in the same order on every build
        ksort($config['arguments']);
        ksort($config['preferences']);
        ksort($config['instanceTypes']);

        $this->configWriter->write($areaCode, $config);
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
