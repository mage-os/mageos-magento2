<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain;

use Magento\Framework\App\Area as AreaCode;
use Magento\Framework\App\AreaList;
use Magento\Framework\App\ObjectManager\ConfigWriterInterface;
use Magento\Setup\Module\Di\App\Task\Operation\Area;
use Magento\Setup\Module\Di\Code\Reader\Decorator\Area as AreaReader;
use Magento\Setup\Module\Di\Compiler\Config\ModificationChain;
use Magento\Setup\Module\Di\Compiler\Config\Reader;
use Magento\Setup\Module\Di\Definition\Collection;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Verifies native proxy eligibility across every area before compiled config is written.
 */
class LazyProxyAreaTest extends TestCase
{
    /**
     * Remove a changing or ineligible target from global and all area configurations.
     */
    #[DataProvider('areaProxyMaps')]
    public function testOnlyTargetsEligibleInEveryAreaAreWritten(array $maps, array $expected): void
    {
        $configs = [];
        foreach ($maps as $scope => $map) {
            $configs[$scope] = [
                'arguments' => [],
                'preferences' => [],
                'instanceTypes' => [],
                'lazyProxies' => $map,
            ];
        }

        $written = $this->compileAreas($configs);

        self::assertSame($expected, $written[AreaCode::AREA_GLOBAL]['lazyProxies']);
        foreach ([AreaCode::AREA_ADMINHTML, AreaCode::AREA_FRONTEND] as $scope) {
            self::assertSame(
                $expected,
                array_replace($written[AreaCode::AREA_GLOBAL]['lazyProxies'], $written[$scope]['lazyProxies'])
            );
        }
    }

    /**
     * Exercise a stable target, preference changes, lost eligibility and area-only entries.
     *
     * @return array
     */
    public static function areaProxyMaps(): array
    {
        $stable = ['Stable\\Proxy' => 'Stable'];
        $global = $stable + ['Changing\\Proxy' => 'Changing'];
        return [
            'same targets everywhere' => [[
                AreaCode::AREA_GLOBAL => $global,
                AreaCode::AREA_ADMINHTML => $global,
                AreaCode::AREA_FRONTEND => $global,
            ], $global],
            'different area preference' => [[
                AreaCode::AREA_GLOBAL => $global,
                AreaCode::AREA_ADMINHTML => $stable + ['Changing\\Proxy' => 'Changing\\Allowed'],
                AreaCode::AREA_FRONTEND => $global,
            ], $stable],
            'last area makes proxy ineligible' => [[
                AreaCode::AREA_GLOBAL => $global,
                AreaCode::AREA_ADMINHTML => $global,
                AreaCode::AREA_FRONTEND => $stable,
            ], $stable],
            'proxy absent in global' => [[
                AreaCode::AREA_GLOBAL => $stable,
                AreaCode::AREA_ADMINHTML => $global,
                AreaCode::AREA_FRONTEND => $global,
            ], $stable],
        ];
    }

    /**
     * Preserve metadata produced on PHP versions without native proxy support.
     */
    public function testAbsentProxySectionsRemainAbsent(): void
    {
        $config = ['arguments' => [], 'preferences' => [], 'instanceTypes' => []];
        $written = $this->compileAreas([
            AreaCode::AREA_GLOBAL => $config,
            AreaCode::AREA_ADMINHTML => $config,
            AreaCode::AREA_FRONTEND => $config,
        ]);

        foreach ($written as $scopeConfig) {
            self::assertArrayNotHasKey('lazyProxies', $scopeConfig);
        }
    }

    /**
     * A missing area allow-list must remove all global entries before runtime merging.
     */
    public function testMissingAreaProxySectionRemovesGlobalTargets(): void
    {
        $config = ['arguments' => [], 'preferences' => [], 'instanceTypes' => []];
        $eligible = $config + ['lazyProxies' => ['Subject\\Proxy' => 'Subject']];
        $written = $this->compileAreas([
            AreaCode::AREA_GLOBAL => $eligible,
            AreaCode::AREA_ADMINHTML => $config,
            AreaCode::AREA_FRONTEND => $eligible,
        ]);

        self::assertSame([], $written[AreaCode::AREA_GLOBAL]['lazyProxies']);
        self::assertArrayNotHasKey('lazyProxies', $written[AreaCode::AREA_ADMINHTML]);
        self::assertSame([], $written[AreaCode::AREA_FRONTEND]['lazyProxies']);
    }

    /**
     * Run the real area assembly and verify that writing waits for all scopes to be inspected.
     *
     * @return array
     */
    private function compileAreas(array $configs): array
    {
        $areaList = $this->createMock(AreaList::class);
        $areaList->method('getCodes')->willReturn([AreaCode::AREA_ADMINHTML, AreaCode::AREA_FRONTEND]);
        $areaReader = $this->createMock(AreaReader::class);
        $areaReader->method('getList')->willReturnMap([['/source', []]]);
        $reader = $this->createMock(Reader::class);
        $generated = [];
        $reader->expects(self::exactly(3))->method('generateCachePerScope')->willReturnCallback(
            static function (Collection $definitions, string $scope) use ($configs, &$generated): array {
                self::assertSame([], $definitions->getCollection());
                $generated[] = $scope;
                return $configs[$scope];
            }
        );
        $written = [];
        $writer = $this->createMock(ConfigWriterInterface::class);
        $writer->expects(self::exactly(3))->method('write')->willReturnCallback(
            static function (string $scope, array $config) use (&$written, &$generated): void {
                self::assertCount(3, $generated);
                $written[$scope] = $config;
            }
        );
        $operation = new Area($areaList, $areaReader, $reader, $writer, new ModificationChain(), ['/source']);

        $operation->doOperation();

        return $written;
    }
}
