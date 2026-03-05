<?php
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Model\VersionCheck;

use Magento\Backend\Model\VersionCheck\LatestVersionFetcher;
use Magento\Backend\Model\VersionCheck\SystemPackageResolver;
use Magento\Backend\Model\VersionCheck\VersionComparison;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class VersionComparisonTest extends TestCase
{
    private LatestVersionFetcher|MockObject $fetcher;
    private SystemPackageResolver|MockObject $packageResolver;
    private VersionComparison $comparison;

    protected function setUp(): void
    {
        $this->fetcher = $this->createMock(LatestVersionFetcher::class);
        $this->packageResolver = $this->createMock(SystemPackageResolver::class);

        $this->comparison = new VersionComparison(
            $this->fetcher,
            $this->packageResolver
        );
    }

    public function testNoUpdateWhenVersionsMatch(): void
    {
        $this->packageResolver->method('getInstalledVersion')->willReturn('2.1.0');
        $this->fetcher->method('getLatestVersion')->willReturn('2.1.0');

        $this->assertFalse($this->comparison->isUpdateAvailable());
    }

    public function testUpdateAvailableWhenNewerExists(): void
    {
        $this->packageResolver->method('getInstalledVersion')->willReturn('2.0.0');
        $this->fetcher->method('getLatestVersion')->willReturn('2.1.0');

        $this->assertTrue($this->comparison->isUpdateAvailable());
    }

    public function testNoUpdateWhenFetcherReturnsNull(): void
    {
        $this->packageResolver->method('getInstalledVersion')->willReturn('2.0.0');
        $this->fetcher->method('getLatestVersion')->willReturn(null);

        $this->assertFalse($this->comparison->isUpdateAvailable());
    }

    public function testNoUpdateWhenNoSystemPackageInstalled(): void
    {
        $this->packageResolver->method('getInstalledVersion')->willReturn(null);
        $this->fetcher->method('getLatestVersion')->willReturn('2.1.0');

        $this->assertFalse($this->comparison->isUpdateAvailable());
    }

    public function testIsMajorUpdateWhenMajorDiffers(): void
    {
        $this->packageResolver->method('getInstalledVersion')->willReturn('1.3.0');
        $this->fetcher->method('getLatestVersion')->willReturn('2.1.0');

        $this->assertTrue($this->comparison->isMajorOrMinorUpdate());
    }

    public function testIsMajorUpdateWhenMinorDiffers(): void
    {
        $this->packageResolver->method('getInstalledVersion')->willReturn('2.0.0');
        $this->fetcher->method('getLatestVersion')->willReturn('2.1.0');

        $this->assertTrue($this->comparison->isMajorOrMinorUpdate());
    }

    public function testIsNotMajorUpdateWhenOnlyPatchDiffers(): void
    {
        $this->packageResolver->method('getInstalledVersion')->willReturn('2.1.0');
        $this->fetcher->method('getLatestVersion')->willReturn('2.1.1');

        $this->assertFalse($this->comparison->isMajorOrMinorUpdate());
    }

    public function testNoUpdateWhenCurrentIsNewer(): void
    {
        $this->packageResolver->method('getInstalledVersion')->willReturn('2.2.0');
        $this->fetcher->method('getLatestVersion')->willReturn('2.1.0');

        $this->assertFalse($this->comparison->isUpdateAvailable());
    }
}
