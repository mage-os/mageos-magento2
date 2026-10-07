<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Setup\Test\Unit\Module\Di\App\Task;

use Magento\Framework\App;
use Magento\Framework\App\AreaList;
use Magento\Framework\App\ObjectManager\ConfigLoader\Compiled as CompiledLoader;
use Magento\Framework\App\ObjectManager\ConfigWriterInterface;
use Magento\Setup\Module\Di\App\Task\Operation\Area;
use Magento\Setup\Module\Di\Compiler\Config;
use Magento\Setup\Module\Di\Compiler\Config\ModificationChain;
use Magento\Setup\Module\Di\Compiler\Config\Reader;
use Magento\Setup\Module\Di\Definition\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AreaTest extends TestCase
{
    /**
     * @var App\AreaList|MockObject
     */
    private $areaListMock;

    /**
     * @var \Magento\Setup\Module\Di\Code\Reader\Decorator\Area|MockObject
     */
    private $areaInstancesNamesList;

    /**
     * @var Config\Reader|MockObject
     */
    private $configReaderMock;

    /**
     * @var Config\WriterInterface|MockObject
     */
    private $configWriterMock;

    /**
     * @var ModificationChain|MockObject
     */
    private $configChain;

    protected function setUp(): void
    {
        $this->areaListMock = $this->getMockBuilder(AreaList::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->areaInstancesNamesList =
            $this->getMockBuilder(\Magento\Setup\Module\Di\Code\Reader\Decorator\Area::class)
                ->disableOriginalConstructor()
                ->getMock();
        $this->configReaderMock = $this->getMockBuilder(Reader::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->configWriterMock =
            $this->getMockBuilder(ConfigWriterInterface::class)
                ->disableOriginalConstructor()
                ->getMock();
        $this->configChain = $this->getMockBuilder(ModificationChain::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testDoOperationEmptyPath()
    {
        $areaOperation = new Area(
            $this->areaListMock,
            $this->areaInstancesNamesList,
            $this->configReaderMock,
            $this->configWriterMock,
            $this->configChain
        );

        $this->assertNull($areaOperation->doOperation());
    }

    public function testDoOperationGlobalArea()
    {
        $path = 'path/to/codebase/';
        $arguments = ['class' => []];
        $generatedConfig = [
            'arguments' => $arguments,
            'preferences' => [],
            'instanceTypes' => []
        ];

        $areaOperation = new Area(
            $this->areaListMock,
            $this->areaInstancesNamesList,
            $this->configReaderMock,
            $this->configWriterMock,
            $this->configChain,
            [$path]
        );

        $this->areaListMock->expects($this->once())
            ->method('getCodes')
            ->willReturn([]);
        $this->areaInstancesNamesList->expects($this->once())
            ->method('getList')
            ->with($path)
            ->willReturn($arguments);
        $this->configReaderMock->expects($this->once())
            ->method('generateCachePerScope')
            ->with(
                $this->isInstanceOf(Collection::class),
                App\Area::AREA_GLOBAL
            )
            ->willReturn($generatedConfig);
        $this->configChain->expects($this->once())
            ->method('modify')
            ->with($generatedConfig)
            ->willReturn($generatedConfig);

        $this->configWriterMock->expects($this->once())
            ->method('write')
            ->with(
                App\Area::AREA_GLOBAL,
                $generatedConfig
            );

        $areaOperation->doOperation();
    }

    public function testDoOperationWritesOnlyTheDifferencesOfNonGlobalAreas()
    {
        $globalConfig = [
            'arguments' => [
                'Overridden' => ['b' => 2],
                'Shared' => ['a' => 1],
            ],
            'preferences' => ['SomeInterface' => 'GlobalImplementation'],
            'instanceTypes' => ['globalVirtual' => 'GlobalType'],
        ];
        $frontendConfig = [
            'arguments' => [
                'FrontendOnly' => ['c' => 3],
                'Overridden' => ['b' => 'frontend'],
                'Shared' => ['a' => 1],
            ],
            'preferences' => ['SomeInterface' => 'FrontendImplementation'],
            'instanceTypes' => ['globalVirtual' => 'GlobalType'],
        ];

        $written = $this->compile([
            App\Area::AREA_GLOBAL => $globalConfig,
            App\Area::AREA_FRONTEND => $frontendConfig,
        ]);

        $this->assertSame($globalConfig, $written[App\Area::AREA_GLOBAL]);
        $this->assertSame(
            [
                CompiledLoader::EXTENDS_KEY => App\Area::AREA_GLOBAL,
                'arguments' => [
                    'FrontendOnly' => ['c' => 3],
                    'Overridden' => ['b' => 'frontend'],
                ],
                'preferences' => ['SomeInterface' => 'FrontendImplementation'],
                'instanceTypes' => [],
            ],
            $written[App\Area::AREA_FRONTEND]
        );
    }

    public function testCompiledLoaderRebuildsTheCompleteAreaFromTheWrittenDifferences()
    {
        $globalConfig = [
            'arguments' => [
                'Overridden' => ['b' => 2],
                'Shared' => ['a' => 1],
                'Falsy' => ['x' => 1],
                'Narrowed' => ['a' => 1, 'b' => 2],
                'LooselyEqual' => ['x' => 0],
            ],
            'preferences' => ['SomeInterface' => 'GlobalImplementation', 'Other' => 'Same'],
            'instanceTypes' => ['globalVirtual' => 'GlobalType'],
            'lazyTypes' => ['Lazy' => true],
        ];
        $frontendConfig = [
            'arguments' => [
                'Falsy' => null,
                'FrontendOnly' => ['c' => 3],
                'FrontendOnlyNull' => null,
                'LooselyEqual' => ['x' => '0'],
                'Narrowed' => ['a' => 1],
                'Overridden' => ['b' => ''],
                'Shared' => ['a' => 1],
            ],
            'preferences' => ['Other' => 'Same', 'SomeInterface' => 'FrontendImplementation'],
            'instanceTypes' => ['globalVirtual' => 'GlobalType', 'frontendVirtual' => 'FrontendType'],
            'lazyTypes' => ['Lazy' => false],
        ];

        $written = $this->compile([
            App\Area::AREA_GLOBAL => $globalConfig,
            App\Area::AREA_FRONTEND => $frontendConfig,
        ]);
        $loader = $this->loaderReading($written);

        $this->assertSameIgnoringKeyOrder($frontendConfig, $loader->load(App\Area::AREA_FRONTEND));
        $this->assertEquals($globalConfig, $loader->load(App\Area::AREA_GLOBAL));
    }

    public function testDoOperationWritesTheWholeConfigurationOfAnAreaThatDropsAGlobalKey()
    {
        $frontendConfig = [
            'arguments' => ['Shared' => ['a' => 1]],
            'preferences' => [],
            'instanceTypes' => [],
        ];

        $written = $this->compile([
            App\Area::AREA_GLOBAL => [
                'arguments' => ['GlobalOnly' => ['b' => 2], 'Shared' => ['a' => 1]],
                'preferences' => [],
                'instanceTypes' => [],
            ],
            App\Area::AREA_FRONTEND => $frontendConfig,
        ]);

        $this->assertSame($frontendConfig, $written[App\Area::AREA_FRONTEND]);
        $this->assertSameIgnoringKeyOrder(
            $frontendConfig,
            $this->loaderReading($written)->load(App\Area::AREA_FRONTEND)
        );
    }

    public function testDoOperationWritesTheWholeConfigurationOfAnAreaThatDropsAGlobalSection()
    {
        $frontendConfig = ['arguments' => [], 'preferences' => [], 'instanceTypes' => []];

        $written = $this->compile([
            App\Area::AREA_GLOBAL => $frontendConfig + ['lazyTypes' => ['Lazy' => true]],
            App\Area::AREA_FRONTEND => $frontendConfig,
        ]);

        $this->assertSame($frontendConfig, $written[App\Area::AREA_FRONTEND]);
        $this->assertSameIgnoringKeyOrder(
            $frontendConfig,
            $this->loaderReading($written)->load(App\Area::AREA_FRONTEND)
        );
    }

    public function testDoOperationWritesTheWholeMergedSectionWhenItIsNotAnArrayInGlobal()
    {
        $frontendConfig = [
            'arguments' => [],
            'preferences' => [],
            'instanceTypes' => [],
            'lazyTypes' => ['Lazy' => true],
        ];

        $written = $this->compile([
            App\Area::AREA_GLOBAL => ['lazyTypes' => 'global'] + $frontendConfig,
            App\Area::AREA_FRONTEND => $frontendConfig,
        ]);

        $this->assertSame(['Lazy' => true], $written[App\Area::AREA_FRONTEND]['lazyTypes']);
        $this->assertSameIgnoringKeyOrder(
            $frontendConfig,
            $this->loaderReading($written)->load(App\Area::AREA_FRONTEND)
        );
    }

    /**
     * Returns a compiled config loader that reads the given files instead of generated/metadata
     *
     * @param array $files
     * @return CompiledLoader
     */
    private function loaderReading(array $files): CompiledLoader
    {
        return new class ($files) extends CompiledLoader {
            /**
             * @param array $files
             */
            public function __construct(private array $files)
            {
            }

            /**
             * @inheritdoc
             */
            protected function loadFile($area)
            {
                return array_key_exists($area, $this->files) ? $this->files[$area] : false;
            }
        };
    }

    /**
     * Asserts that a resolved configuration holds exactly the expected sections, keys and values
     *
     * @param array $expected
     * @param array $actual
     * @return void
     */
    private function assertSameIgnoringKeyOrder(array $expected, array $actual): void
    {
        ksort($expected);
        ksort($actual);
        $this->assertSame(array_keys($expected), array_keys($actual));
        foreach ($expected as $section => $values) {
            if (is_array($values) && is_array($actual[$section])) {
                ksort($values);
                ksort($actual[$section]);
            }
            $this->assertSame($values, $actual[$section], $section);
        }
    }

    /**
     * Runs the area operation over the given configurations and returns what it writes, per area
     *
     * @param array $configsByArea
     * @return array
     */
    private function compile(array $configsByArea): array
    {
        $path = 'path/to/codebase/';
        $areaOperation = new Area(
            $this->areaListMock,
            $this->areaInstancesNamesList,
            $this->configReaderMock,
            $this->configWriterMock,
            $this->configChain,
            [$path]
        );

        $this->areaListMock->expects($this->once())
            ->method('getCodes')
            ->willReturn(array_values(array_diff(array_keys($configsByArea), [App\Area::AREA_GLOBAL])));
        $this->areaInstancesNamesList->expects($this->once())
            ->method('getList')
            ->with($path)
            ->willReturn([]);
        $this->configReaderMock->expects($this->exactly(count($configsByArea)))
            ->method('generateCachePerScope')
            ->willReturnCallback(static fn (...$arguments) => $configsByArea[$arguments[1]]);
        $this->configChain->expects($this->exactly(count($configsByArea)))
            ->method('modify')
            ->willReturnArgument(0);

        $written = [];
        $this->configWriterMock->expects($this->exactly(count($configsByArea)))
            ->method('write')
            ->willReturnCallback(function ($areaCode, $config) use (&$written) {
                $written[$areaCode] = $config;
            });

        $areaOperation->doOperation();

        return $written;
    }
}
