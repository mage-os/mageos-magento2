<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Interception\Test\Unit;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Config\ReaderInterface;
use Magento\Framework\Config\ScopeInterface;
use Magento\Framework\Interception\ConfigInterface as InterceptionConfigInterface;
use Magento\Framework\Interception\Definition\Runtime as InterceptionDefinitionRuntime;
use Magento\Framework\Interception\ObjectManager\ConfigInterface;
use Magento\Framework\Interception\PluginListGenerator;
use Magento\Framework\Interception\Test\Unit\Custom\Module\Model\Item;
use Magento\Framework\Interception\Test\Unit\Custom\Module\Model\ItemPlugin\Simple as ItemPluginSimple;
use Magento\Framework\ObjectManager\ConfigCacheInterface;
use Magento\Framework\ObjectManager\DefinitionInterface as ClassDefinitionsInterface;
use Magento\Framework\ObjectManager\Relations\Runtime as RelationsRuntime;
use Magento\Framework\ObjectManager\RelationsInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

require_once __DIR__ . '/Custom/Module/Model/Item.php';
require_once __DIR__ . '/Custom/Module/Model/ItemPlugin/Simple.php';

/**
 * Regression test for mage-os/mageos-magento2#299.
 *
 * A plugin declared in the *global* scope must end up in the compiled
 * plugin-list of *every* area, not just the first area processed. The
 * DI-compile "area order" change made setup:di:compile truncate the
 * plugin-list of every area except the first, dropping global plugins in
 * (most visibly) the `graphql` area.
 */
class PluginListGeneratorTest extends TestCase
{
    /**
     * @var string
     */
    private string $tmpDir;

    protected function setUp(): void
    {
        $this->tmpDir = sys_get_temp_dir() . '/plugin_list_generator_test_' . uniqid('', true);
        mkdir($this->tmpDir . '/generated/metadata', 0777, true);
    }

    protected function tearDown(): void
    {
        foreach (glob($this->tmpDir . '/generated/metadata/*.php') ?: [] as $file) {
            unlink($file);
        }
        @rmdir($this->tmpDir . '/generated/metadata');
        @rmdir($this->tmpDir . '/generated');
        @rmdir($this->tmpDir);
    }

    /**
     * Compiles the standard Magento area set (the exact order returned by
     * ScopeInterface::getAllScopes()) with a single global plugin on Item,
     * then asserts that plugin survives into every compiled area.
     */
    public function testGlobalPluginIsCompiledIntoEveryArea(): void
    {
        // Same scope set/order a real install yields from getAllScopes().
        $scopes = ['primary', 'global', 'frontend', 'adminhtml', 'graphql', 'crontab', 'webapi_rest', 'webapi_soap'];

        $this->createGenerator()->write($scopes);

        $areasMissingGlobalPlugin = [];
        foreach (glob($this->tmpDir . '/generated/metadata/*|plugin-list.php') as $file) {
            [$pluginData, $inherited] = include $file;
            $hasGlobalPlugin = isset($pluginData[Item::class]) || isset($inherited[Item::class]);
            if (!$hasGlobalPlugin) {
                $areasMissingGlobalPlugin[] = basename($file, '|plugin-list.php');
            }
        }

        $this->assertSame(
            [],
            $areasMissingGlobalPlugin,
            'Global plugin on Item is missing from these compiled area plugin-lists: '
            . implode(', ', $areasMissingGlobalPlugin)
        );
    }

    /**
     * @return PluginListGenerator
     */
    private function createGenerator(): PluginListGenerator
    {
        $reader = new class implements ReaderInterface {
            public function read($scope = null)
            {
                if ($scope === 'global') {
                    return [
                        Item::class => [
                            'plugins' => [
                                'simple_plugin' => [
                                    'sortOrder' => 10,
                                    'instance' => ItemPluginSimple::class,
                                ],
                            ],
                        ],
                    ];
                }
                return [];
            }
        };

        $scopeConfig = new class implements ScopeInterface {
            private $currentScope = 'global';
            public function getCurrentScope()
            {
                return $this->currentScope;
            }
            public function setCurrentScope($scope)
            {
                $this->currentScope = $scope;
            }
        };

        $omConfig = new class implements ConfigInterface {
            public function setInterceptionConfig(InterceptionConfigInterface $interceptionConfig)
            {
            }
            public function getOriginalInstanceType($instanceName)
            {
                return $instanceName;
            }
            public function setRelations(RelationsInterface $relations)
            {
            }
            public function setCache(ConfigCacheInterface $cache)
            {
            }
            public function getArguments($type)
            {
                return [];
            }
            public function isShared($type)
            {
                return true;
            }
            public function getInstanceType($instanceName)
            {
                return $instanceName;
            }
            public function getPreference($type)
            {
                return $type;
            }
            public function getVirtualTypes()
            {
                return [];
            }
            public function extend(array $configuration)
            {
            }
            public function getPreferences()
            {
                return [];
            }
        };

        $classDefinitions = new class implements ClassDefinitionsInterface {
            public function getParameters($className)
            {
                return [];
            }
            public function getClasses()
            {
                return [Item::class];
            }
        };

        return new PluginListGenerator(
            $reader,
            $scopeConfig,
            $omConfig,
            new RelationsRuntime(),
            new InterceptionDefinitionRuntime(),
            $classDefinitions,
            new NullLogger(),
            new DirectoryList($this->tmpDir),
            ['global'],
            'production'
        );
    }
}
