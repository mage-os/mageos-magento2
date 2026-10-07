<?php
/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\App\Test\Unit\ObjectManager\ConfigLoader;

use Magento\Framework\App\ObjectManager\ConfigLoader\Compiled;
use Magento\Framework\ObjectManager\Config\Compiled as CompiledConfig;
use Magento\Framework\ObjectManager\ConfigLoaderInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class CompiledTest extends TestCase
{
    /**
     * Returns a loader that reads the given file contents instead of generated/metadata
     *
     * @param array $files
     * @return Compiled
     */
    private function loaderFor(array $files): Compiled
    {
        return new class ($files) extends Compiled {
            /**
             * @var int
             */
            public $fileReads = 0;

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
                $this->fileReads++;

                return $this->files[$area] ?? false;
            }
        };
    }

    public function testLoadReturnsAnUnmarkedConfigurationUnchanged(): void
    {
        $frontend = [
            'arguments' => ['Type' => ['a' => 1]],
            'preferences' => ['Interface' => 'Implementation'],
            'instanceTypes' => [],
            'customSection' => 'value',
        ];

        $this->assertSame($frontend, $this->loaderFor(['frontend' => $frontend])->load('frontend'));
    }

    public function testLoadReturnsAnUnmarkedNonAreaFileUnchanged(): void
    {
        $pluginList = ['some' => ['plugin' => 'data']];

        $this->assertSame($pluginList, $this->loaderFor(['global|plugin-list' => $pluginList])
            ->load('global|plugin-list'));
    }

    public function testLoadReturnsTheCompleteConfigurationOfAMarkedFile(): void
    {
        $loader = $this->loaderFor([
            'global' => [
                'arguments' => ['Shared' => ['a' => 1], 'Overridden' => ['b' => 2]],
                'preferences' => ['Interface' => 'GlobalImplementation'],
                'instanceTypes' => ['virtual' => 'GlobalType'],
                'lazyTypes' => ['Lazy' => true],
            ],
            'frontend' => [
                ConfigLoaderInterface::EXTENDS_KEY => 'global',
                'arguments' => ['Overridden' => ['b' => 'frontend'], 'FrontendOnly' => ['c' => 3]],
                'preferences' => ['Interface' => 'FrontendImplementation'],
                'instanceTypes' => [],
            ],
        ]);

        $this->assertSame(
            [
                'arguments' => [
                    'Shared' => ['a' => 1],
                    'Overridden' => ['b' => 'frontend'],
                    'FrontendOnly' => ['c' => 3],
                ],
                'preferences' => ['Interface' => 'FrontendImplementation'],
                'instanceTypes' => ['virtual' => 'GlobalType'],
                'lazyTypes' => ['Lazy' => true],
            ],
            $loader->load('frontend')
        );
    }

    #[DataProvider('mergedSections')]
    public function testLoadResolvesEverySectionAsArrayReplaceOfTheBase(string $section): void
    {
        $global = [$section => ['Shared' => 'globalValue', 'Overridden' => 'globalValue', 'Falsy' => 'value']];
        $delta = [$section => ['Overridden' => 'areaValue', 'AreaOnly' => 'areaValue', 'Falsy' => null]];
        $loader = $this->loaderFor([
            'global' => $global,
            'frontend' => [ConfigLoaderInterface::EXTENDS_KEY => 'global'] + $delta,
        ]);

        $this->assertSame(
            array_replace($global[$section], $delta[$section]),
            $loader->load('frontend')[$section]
        );
    }

    /**
     * @return array
     */
    public static function mergedSections(): array
    {
        return array_map(static fn (string $section) => [$section], CompiledConfig::MERGED_SECTIONS);
    }

    public function testLoadReplacesSectionsThatAreNotMerged(): void
    {
        $loader = $this->loaderFor([
            'global' => ['arguments' => [], 'other' => ['a' => 1, 'b' => 2]],
            'frontend' => [
                ConfigLoaderInterface::EXTENDS_KEY => 'global',
                'arguments' => [],
                'other' => ['b' => 3],
            ],
        ]);

        $this->assertSame(['b' => 3], $loader->load('frontend')['other']);
    }

    public function testLoadDoesNotCacheTheResolvedConfiguration(): void
    {
        $loader = $this->loaderFor([
            'global' => ['arguments' => ['Shared' => 1]],
            'frontend' => [ConfigLoaderInterface::EXTENDS_KEY => 'global', 'arguments' => ['Own' => 2]],
        ]);

        $this->assertSame($loader->load('frontend'), $loader->load('frontend'));
        $this->assertSame(4, $loader->fileReads, 'each load must resolve the delta again');
    }

    #[DataProvider('invalidBases')]
    public function testLoadFailsOnAnInvalidBase($base): void
    {
        $loader = $this->loaderFor([
            'frontend' => [ConfigLoaderInterface::EXTENDS_KEY => $base, 'arguments' => []],
        ]);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('extends an invalid area');

        $loader->load('frontend');
    }

    /**
     * @return array
     */
    public static function invalidBases(): array
    {
        return [
            'null' => [null],
            'empty' => [''],
            'array' => [['global']],
            'itself' => ['frontend'],
        ];
    }

    public function testLoadFailsWhenTheBaseCannotBeLoaded(): void
    {
        $loader = $this->loaderFor([
            'frontend' => [ConfigLoaderInterface::EXTENDS_KEY => 'global', 'arguments' => []],
        ]);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('could not be loaded');

        $loader->load('frontend');
    }

    public function testLoadFailsWhenTheBaseIsItselfADelta(): void
    {
        $loader = $this->loaderFor([
            'global' => [ConfigLoaderInterface::EXTENDS_KEY => 'frontend', 'arguments' => []],
            'frontend' => [ConfigLoaderInterface::EXTENDS_KEY => 'global', 'arguments' => []],
        ]);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('itself a delta');

        $loader->load('frontend');
    }
}
