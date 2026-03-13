<?php
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Model\VersionCheck;

use Composer\Semver\VersionParser;
use Magento\Backend\Model\VersionCheck\LatestVersionFetcher;
use Magento\Backend\Model\VersionCheck\SystemPackageResolver;
use Magento\Backend\Model\VersionCheck\VersionComparison;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class VersionComparisonTest extends TestCase
{
    /**
     * @var LatestVersionFetcher|MockObject
     */
    private LatestVersionFetcher|MockObject $fetcher;

    /**
     * @var SystemPackageResolver|MockObject
     */
    private SystemPackageResolver|MockObject $packageResolver;

    /**
     * @var VersionParser
     */
    private VersionParser $versionParser;

    /**
     * @var LoggerInterface|MockObject
     */
    private LoggerInterface|MockObject $logger;

    /**
     * @var VersionComparison
     */
    private VersionComparison $comparison;

    protected function setUp(): void
    {
        $this->fetcher = $this->createMock(LatestVersionFetcher::class);
        $this->packageResolver = $this->createMock(SystemPackageResolver::class);
        $this->versionParser = new VersionParser();
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->comparison = new VersionComparison(
            $this->fetcher,
            $this->packageResolver,
            $this->versionParser,
            $this->logger
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

    public function testIsMajorOrMinorUpdateReturnsFalseWhenFetcherReturnsNull(): void
    {
        $this->packageResolver->method('getInstalledVersion')->willReturn('2.0.0');
        $this->fetcher->method('getLatestVersion')->willReturn(null);

        $this->assertFalse($this->comparison->isMajorOrMinorUpdate());
    }

    public function testIsMajorOrMinorUpdateReturnsFalseWhenNoSystemPackage(): void
    {
        $this->packageResolver->method('getInstalledVersion')->willReturn(null);
        $this->fetcher->method('getLatestVersion')->willReturn('2.1.0');

        $this->assertFalse($this->comparison->isMajorOrMinorUpdate());
    }

    public function testIsMajorOrMinorUpdateWithPatchSuffix(): void
    {
        $this->packageResolver->method('getInstalledVersion')->willReturn('2.4.8-p4');
        $this->fetcher->method('getLatestVersion')->willReturn('2.5.0');

        $this->assertTrue($this->comparison->isMajorOrMinorUpdate());
    }

    public function testIsNotMajorOrMinorUpdateWithPatchSuffixSameMinor(): void
    {
        $this->packageResolver->method('getInstalledVersion')->willReturn('2.4.8-p3');
        $this->fetcher->method('getLatestVersion')->willReturn('2.4.8-p4');

        $this->assertFalse($this->comparison->isMajorOrMinorUpdate());
    }

    public function testIsMajorOrMinorUpdateLogsWarningOnNormalizationFailure(): void
    {
        $this->packageResolver->method('getInstalledVersion')->willReturn('not-a-version');
        $this->fetcher->method('getLatestVersion')->willReturn('2.1.0');

        $this->logger->expects($this->once())->method('warning');

        $this->assertFalse($this->comparison->isMajorOrMinorUpdate());
    }

    public function testFetcherCalledOnlyOnceAcrossMultipleMethods(): void
    {
        $this->fetcher->expects($this->once())->method('getLatestVersion')->willReturn('2.1.0');
        $this->packageResolver->expects($this->once())->method('getInstalledVersion')->willReturn('2.0.0');

        $this->comparison->isUpdateAvailable();
        $this->comparison->isMajorOrMinorUpdate();
        $this->comparison->getLatestVersion();
        $this->comparison->getCurrentVersion();
    }
}
