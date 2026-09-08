<?php
/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Deploy\Test\Unit\Service;

use Magento\Deploy\Config\BundleConfig;
use Magento\Deploy\Package\BundleInterface;
use Magento\Deploy\Package\BundleInterfaceFactory;
use Magento\Deploy\Service\Bundle;
use Magento\Framework\App\Utility\Files;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\Filesystem\Io\File;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Bundle composition must not depend on the order in which deployed files are discovered.
 */
class BundleTest extends TestCase
{
    /** @var MockObject|WriteInterface */
    private $pubStaticDir;

    /** @var MockObject|BundleInterface */
    private $bundle;

    /** @var Bundle */
    private $service;

    protected function setUp(): void
    {
        $this->pubStaticDir = $this->getMockForAbstractClass(WriteInterface::class);
        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->method('getDirectoryWrite')->willReturn($this->pubStaticDir);

        $this->bundle = $this->getMockForAbstractClass(BundleInterface::class);
        $bundleFactory = $this->createMock(BundleInterfaceFactory::class);
        $bundleFactory->method('create')->willReturn($this->bundle);

        $bundleConfig = $this->createMock(BundleConfig::class);
        $bundleConfig->method('getExcludedFiles')->willReturn([]);
        $bundleConfig->method('getExcludedDirectories')->willReturn([]);

        $file = $this->createMock(File::class);
        $file->method('getPathInfo')->willReturnCallback(static fn($path) => pathinfo($path));

        $this->service = new Bundle(
            $filesystem,
            $bundleFactory,
            $bundleConfig,
            $this->createMock(Files::class),
            $file
        );
    }

    /**
     * Files are packed into numbered bundles in iteration order, so the order the map file
     * happens to list them in must not decide which bundle a file lands in.
     */
    public function testFilesAreBundledInPathOrderRegardlessOfMapOrder()
    {
        $expected = ['alpha.js', 'middle.js', 'zebra.js'];

        $this->assertSame(
            $expected,
            $this->collectBundledPaths(['zebra.js', 'alpha.js', 'middle.js']),
            'files should be bundled in sorted order'
        );
        $this->assertSame(
            $expected,
            $this->collectBundledPaths(['middle.js', 'zebra.js', 'alpha.js']),
            'a different discovery order must not change bundle composition'
        );
    }

    /**
     * hasMinVersion() remembers the unminified twin of every ".min." file it passes, so without a
     * stable order a file could be bundled or skipped depending on which of the pair came first.
     */
    public function testMinifiedTwinHandlingDoesNotDependOnDiscoveryOrder()
    {
        $this->assertSame(
            $this->collectBundledPaths(['widget.js', 'widget.min.js']),
            $this->collectBundledPaths(['widget.min.js', 'widget.js']),
            'min/non-min handling must not depend on discovery order'
        );
    }

    /**
     * Drive deploy() from the map-file branch and return the paths handed to the bundle.
     *
     * @param string[] $mapOrder
     * @return string[]
     */
    private function collectBundledPaths(array $mapOrder): array
    {
        $map = [];
        foreach ($mapOrder as $path) {
            $map[$path] = ['area' => 'frontend', 'theme' => 'Test/theme', 'locale' => 'en_US'];
        }

        $this->pubStaticDir->method('isFile')->willReturn(true);
        $this->pubStaticDir->method('readFile')->willReturn(json_encode($map));
        $this->pubStaticDir->method('isExist')->willReturn(false);

        $collected = [];
        $bundle = $this->getMockForAbstractClass(BundleInterface::class);
        $bundle->method('addFile')->willReturnCallback(
            function ($filePath) use (&$collected) {
                $collected[] = $filePath;
                return true;
            }
        );
        $bundleFactory = $this->createMock(BundleInterfaceFactory::class);
        $bundleFactory->method('create')->willReturn($bundle);

        $filesystem = $this->createMock(Filesystem::class);
        $filesystem->method('getDirectoryWrite')->willReturn($this->pubStaticDir);
        $bundleConfig = $this->createMock(BundleConfig::class);
        $bundleConfig->method('getExcludedFiles')->willReturn([]);
        $bundleConfig->method('getExcludedDirectories')->willReturn([]);
        $file = $this->createMock(File::class);
        $file->method('getPathInfo')->willReturnCallback(static fn($path) => pathinfo($path));

        $service = new Bundle($filesystem, $bundleFactory, $bundleConfig, $this->createMock(Files::class), $file);
        $service->deploy('frontend', 'Test/theme', 'en_US');

        return $collected;
    }
}
