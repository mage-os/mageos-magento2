<?php
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Model\VersionCheck;

use Composer\Semver\VersionParser;
use Magento\Backend\Model\VersionCheck\LatestVersionFetcher;
use Magento\Backend\Model\VersionCheck\SystemPackageResolver;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Composer\ComposerInformation;
use Magento\Framework\HTTP\ClientInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class LatestVersionFetcherTest extends TestCase
{
    /**
     * @var ClientInterface|MockObject
     */
    private ClientInterface|MockObject $httpClient;

    /**
     * @var CacheInterface|MockObject
     */
    private CacheInterface|MockObject $cache;

    /**
     * @var LoggerInterface|MockObject
     */
    private LoggerInterface|MockObject $logger;

    /**
     * @var SystemPackageResolver|MockObject
     */
    private SystemPackageResolver|MockObject $packageResolver;

    /**
     * @var ComposerInformation|MockObject
     */
    private ComposerInformation|MockObject $composerInformation;

    /**
     * @var ScopeConfigInterface|MockObject
     */
    private ScopeConfigInterface|MockObject $scopeConfig;

    /**
     * @var VersionParser
     */
    private VersionParser $versionParser;

    /**
     * @var LatestVersionFetcher
     */
    private LatestVersionFetcher $fetcher;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(ClientInterface::class);
        $this->cache = $this->createMock(CacheInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->packageResolver = $this->createMock(SystemPackageResolver::class);
        $this->composerInformation = $this->createMock(ComposerInformation::class);
        $this->scopeConfig = $this->createMock(ScopeConfigInterface::class);

        $this->scopeConfig->method('isSetFlag')
            ->with(LatestVersionFetcher::XML_PATH_ENABLED)
            ->willReturn(true);
        $this->scopeConfig->method('getValue')
            ->with(LatestVersionFetcher::XML_PATH_CACHE_LIFETIME)
            ->willReturn('86400');

        $this->composerInformation->method('getRootRepositories')
            ->willReturn(['https://repo.mage-os.org']);

        $this->packageResolver->method('getPackageName')
            ->willReturn('mage-os/product-community-edition');

        $this->versionParser = new VersionParser();

        $this->fetcher = new LatestVersionFetcher(
            $this->httpClient,
            $this->cache,
            $this->logger,
            $this->packageResolver,
            $this->composerInformation,
            $this->scopeConfig,
            $this->versionParser
        );
    }

    // --- getLatestVersion (cache-read only) ---

    public function testGetLatestVersionReturnsCachedVersion(): void
    {
        $this->cache->method('load')->willReturn('2.1.0');
        $this->httpClient->expects($this->never())->method('get');

        $this->assertSame('2.1.0', $this->fetcher->getLatestVersion());
    }

    public function testGetLatestVersionReturnsNullOnCacheMiss(): void
    {
        $this->cache->method('load')->willReturn(false);
        $this->httpClient->expects($this->never())->method('get');

        $this->assertNull($this->fetcher->getLatestVersion());
    }

    public function testGetLatestVersionReturnsNullOnNegativeCacheSentinel(): void
    {
        $this->cache->method('load')->willReturn('');
        $this->httpClient->expects($this->never())->method('get');

        $this->assertNull($this->fetcher->getLatestVersion());
    }

    public function testGetLatestVersionReturnsNullWhenDisabled(): void
    {
        $scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $scopeConfig->method('isSetFlag')
            ->with(LatestVersionFetcher::XML_PATH_ENABLED)
            ->willReturn(false);

        $fetcher = new LatestVersionFetcher(
            $this->httpClient,
            $this->cache,
            $this->logger,
            $this->packageResolver,
            $this->composerInformation,
            $scopeConfig,
            $this->versionParser
        );

        $this->httpClient->expects($this->never())->method('get');
        $this->assertNull($fetcher->getLatestVersion());
    }

    public function testGetLatestVersionReturnsNullWhenNoSystemPackage(): void
    {
        $packageResolver = $this->createMock(SystemPackageResolver::class);
        $packageResolver->method('getPackageName')->willReturn(null);

        $fetcher = new LatestVersionFetcher(
            $this->httpClient,
            $this->cache,
            $this->logger,
            $packageResolver,
            $this->composerInformation,
            $this->scopeConfig,
            $this->versionParser
        );

        $this->httpClient->expects($this->never())->method('get');
        $this->assertNull($fetcher->getLatestVersion());
    }

    // --- fetchAndCache (HTTP + cache write) ---

    public function testFetchAndCacheFetchesAndCachesVersion(): void
    {
        $this->cache->method('load')->willReturn(false);

        $responseBody = json_encode([
            'packages' => [
                'mage-os/product-community-edition' => [
                    ['version' => '1.0.0'],
                    ['version' => '1.1.0'],
                    ['version' => '2.1.0'],
                ],
            ],
        ]);

        $this->httpClient->expects($this->once())
            ->method('get')
            ->with('https://repo.mage-os.org/p2/mage-os/product-community-edition.json');
        $this->httpClient->method('getStatus')->willReturn(200);
        $this->httpClient->method('getBody')->willReturn($responseBody);

        $this->cache->expects($this->once())
            ->method('save')
            ->with(
                '2.1.0',
                $this->anything(),
                [],
                $this->logicalAnd($this->greaterThanOrEqual(86400 - 7200), $this->lessThanOrEqual(86400 + 7200))
            );

        $this->assertSame('2.1.0', $this->fetcher->fetchAndCache());
    }

    public function testFetchAndCacheSkipsHttpWhenCacheIsWarm(): void
    {
        $this->cache->method('load')->willReturn('2.1.0');
        $this->httpClient->expects($this->never())->method('get');

        $this->assertSame('2.1.0', $this->fetcher->fetchAndCache());
    }

    public function testFetchAndCacheSkipsHttpWhenNegativeCacheIsWarm(): void
    {
        $this->cache->method('load')->willReturn('');
        $this->httpClient->expects($this->never())->method('get');

        $this->assertNull($this->fetcher->fetchAndCache());
    }

    public function testFetchAndCacheWritesNegativeCacheOnFailure(): void
    {
        $this->cache->method('load')->willReturn(false);
        $this->httpClient->method('getStatus')->willReturn(500);

        $this->cache->expects($this->once())
            ->method('save')
            ->with('', $this->anything(), [], 300);

        $this->assertNull($this->fetcher->fetchAndCache());
    }

    public function testFetchAndCacheWritesNegativeCacheOnEmptyPackageList(): void
    {
        $this->cache->method('load')->willReturn(false);

        $responseBody = json_encode(['packages' => ['mage-os/product-community-edition' => []]]);
        $this->httpClient->method('getStatus')->willReturn(200);
        $this->httpClient->method('getBody')->willReturn($responseBody);

        $this->cache->expects($this->once())
            ->method('save')
            ->with('', $this->anything(), [], 300);

        $this->assertNull($this->fetcher->fetchAndCache());
    }

    public function testFetchAndCacheReturnsNullWhenDisabled(): void
    {
        $scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $scopeConfig->method('isSetFlag')
            ->with(LatestVersionFetcher::XML_PATH_ENABLED)
            ->willReturn(false);

        $fetcher = new LatestVersionFetcher(
            $this->httpClient,
            $this->cache,
            $this->logger,
            $this->packageResolver,
            $this->composerInformation,
            $scopeConfig,
            $this->versionParser
        );

        $this->httpClient->expects($this->never())->method('get');
        $this->assertNull($fetcher->fetchAndCache());
    }

    public function testFetchAndCacheReturnsNullWhenNoSystemPackage(): void
    {
        $packageResolver = $this->createMock(SystemPackageResolver::class);
        $packageResolver->method('getPackageName')->willReturn(null);

        $fetcher = new LatestVersionFetcher(
            $this->httpClient,
            $this->cache,
            $this->logger,
            $packageResolver,
            $this->composerInformation,
            $this->scopeConfig,
            $this->versionParser
        );

        $this->httpClient->expects($this->never())->method('get');
        $this->assertNull($fetcher->fetchAndCache());
    }

    public function testFetchAndCacheSkipsInvalidUrls(): void
    {
        $composerInformation = $this->createMock(ComposerInformation::class);
        $composerInformation->method('getRootRepositories')
            ->willReturn(['/local/path', '', 'https://repo.mage-os.org']);

        $fetcher = new LatestVersionFetcher(
            $this->httpClient,
            $this->cache,
            $this->logger,
            $this->packageResolver,
            $composerInformation,
            $this->scopeConfig,
            $this->versionParser
        );

        $this->cache->method('load')->willReturn(false);

        $responseBody = json_encode([
            'packages' => [
                'mage-os/product-community-edition' => [['version' => '2.0.0']],
            ],
        ]);

        $this->httpClient->expects($this->once())
            ->method('get')
            ->with('https://repo.mage-os.org/p2/mage-os/product-community-edition.json');
        $this->httpClient->method('getStatus')->willReturn(200);
        $this->httpClient->method('getBody')->willReturn($responseBody);

        $this->logger->expects($this->atLeast(2))
            ->method('debug')
            ->with($this->stringContains('skipping invalid repository URL'));

        $this->assertSame('2.0.0', $fetcher->fetchAndCache());
    }

    public function testFetchAndCacheTriesMultipleReposAndUsesFirstSuccessful(): void
    {
        $composerInformation = $this->createMock(ComposerInformation::class);
        $composerInformation->method('getRootRepositories')
            ->willReturn(['https://first-repo.example.com', 'https://second-repo.example.com']);

        $fetcher = new LatestVersionFetcher(
            $this->httpClient,
            $this->cache,
            $this->logger,
            $this->packageResolver,
            $composerInformation,
            $this->scopeConfig,
            $this->versionParser
        );

        $this->cache->method('load')->willReturn(false);

        $responseBody = json_encode([
            'packages' => [
                'mage-os/product-community-edition' => [['version' => '3.0.0']],
            ],
        ]);

        $statusCallCount = 0;
        $this->httpClient->method('getStatus')->willReturnCallback(function () use (&$statusCallCount) {
            $statusCallCount++;
            return $statusCallCount === 1 ? 404 : 200;
        });
        $this->httpClient->method('getBody')->willReturn($responseBody);

        $this->assertSame('3.0.0', $fetcher->fetchAndCache());
    }

    public function testFetchAndCacheSetsTimeoutTo15Seconds(): void
    {
        $this->cache->method('load')->willReturn(false);

        $this->httpClient->expects($this->once())
            ->method('setTimeout')
            ->with(15);
        $this->httpClient->method('getStatus')->willReturn(200);
        $this->httpClient->method('getBody')->willReturn(json_encode([
            'packages' => ['mage-os/product-community-edition' => [['version' => '1.0.0']]],
        ]));

        $this->fetcher->fetchAndCache();
    }

    public function testFetchAndCacheLogsNon200HttpStatus(): void
    {
        $this->cache->method('load')->willReturn(false);
        $this->httpClient->method('getStatus')->willReturn(403);

        $this->logger->expects($this->once())
            ->method('debug')
            ->with($this->stringContains('HTTP 403'));

        $this->fetcher->fetchAndCache();
    }

    public function testFetchAndCacheLogsAndContinuesOnRuntimeException(): void
    {
        $this->cache->method('load')->willReturn(false);

        $this->httpClient->method('get')
            ->willThrowException(new \RuntimeException('Connection timed out'));

        $this->logger->expects($this->once())
            ->method('info')
            ->with($this->stringContains('Connection timed out'));

        $this->cache->expects($this->once())
            ->method('save')
            ->with('', $this->anything(), [], 300);

        $this->assertNull($this->fetcher->fetchAndCache());
    }

    public function testFetchAndCacheLogsWarningOnUnexpectedException(): void
    {
        $this->cache->method('load')->willReturn(false);

        $exception = new \LogicException('Unexpected error');
        $this->httpClient->method('get')
            ->willThrowException($exception);

        $this->logger->expects($this->once())
            ->method('warning')
            ->with(
                'Unexpected error during version check',
                ['exception' => $exception]
            );

        $this->assertNull($this->fetcher->fetchAndCache());
    }

    public function testFetchAndCacheFiltersOutNonStableVersions(): void
    {
        $this->cache->method('load')->willReturn(false);

        $responseBody = json_encode([
            'packages' => [
                'mage-os/product-community-edition' => [
                    ['version' => '2.0.0'],
                    ['version' => '2.1.0'],
                    ['version' => '3.0.0-beta1'],
                ],
            ],
        ]);

        $this->httpClient->method('getStatus')->willReturn(200);
        $this->httpClient->method('getBody')->willReturn($responseBody);

        $this->assertSame('2.1.0', $this->fetcher->fetchAndCache());
    }

    public function testFetchAndCacheFindsHighestVersionRegardlessOfOrder(): void
    {
        $this->cache->method('load')->willReturn(false);

        $responseBody = json_encode([
            'packages' => [
                'mage-os/product-community-edition' => [
                    ['version' => '2.1.0'],
                    ['version' => '1.0.0'],
                    ['version' => '2.0.0'],
                    ['version' => '1.3.1'],
                ],
            ],
        ]);

        $this->httpClient->method('getStatus')->willReturn(200);
        $this->httpClient->method('getBody')->willReturn($responseBody);

        $this->assertSame('2.1.0', $this->fetcher->fetchAndCache());
    }

    public function testFetchAndCacheHandlesMalformedJsonResponse(): void
    {
        $this->cache->method('load')->willReturn(false);

        $this->httpClient->method('getStatus')->willReturn(200);
        $this->httpClient->method('getBody')->willReturn('<html>Error page</html>');

        $this->logger->expects($this->once())
            ->method('info')
            ->with($this->stringContains('non-JSON response'));

        $this->assertNull($this->fetcher->fetchAndCache());
    }

    public function testFetchAndCacheSkipsUnparseableVersionStrings(): void
    {
        $this->cache->method('load')->willReturn(false);

        $responseBody = json_encode([
            'packages' => [
                'mage-os/product-community-edition' => [
                    ['version' => 'not-a-version'],
                    ['version' => '2.0.0'],
                ],
            ],
        ]);

        $this->httpClient->method('getStatus')->willReturn(200);
        $this->httpClient->method('getBody')->willReturn($responseBody);

        $this->assertSame('2.0.0', $this->fetcher->fetchAndCache());
    }

    public function testFetchAndCacheReturnsVersionEvenWhenCacheSaveFails(): void
    {
        $this->cache->method('load')->willReturn(false);

        $responseBody = json_encode([
            'packages' => [
                'mage-os/product-community-edition' => [['version' => '2.1.0']],
            ],
        ]);

        $this->httpClient->method('getStatus')->willReturn(200);
        $this->httpClient->method('getBody')->willReturn($responseBody);
        $this->cache->method('save')
            ->willThrowException(new \RuntimeException('Cache backend unavailable'));

        $this->logger->expects($this->once())
            ->method('warning')
            ->with($this->stringContains('Failed to cache'));

        $this->assertSame('2.1.0', $this->fetcher->fetchAndCache());
    }

    public function testFetchAndCacheLogsDebugWhenNoSystemPackageDetected(): void
    {
        $packageResolver = $this->createMock(SystemPackageResolver::class);
        $packageResolver->method('getPackageName')->willReturn(null);

        $fetcher = new LatestVersionFetcher(
            $this->httpClient,
            $this->cache,
            $this->logger,
            $packageResolver,
            $this->composerInformation,
            $this->scopeConfig,
            $this->versionParser
        );

        $this->logger->expects($this->once())
            ->method('debug')
            ->with($this->stringContains('no system package'));

        $fetcher->fetchAndCache();
    }

    public function testFetchAndCacheNoReposConfigured(): void
    {
        $composerInformation = $this->createMock(ComposerInformation::class);
        $composerInformation->method('getRootRepositories')->willReturn([]);

        $fetcher = new LatestVersionFetcher(
            $this->httpClient,
            $this->cache,
            $this->logger,
            $this->packageResolver,
            $composerInformation,
            $this->scopeConfig,
            $this->versionParser
        );

        $this->cache->method('load')->willReturn(false);
        $this->httpClient->expects($this->never())->method('get');

        $this->cache->expects($this->once())
            ->method('save')
            ->with('', $this->anything(), [], 300);

        $this->assertNull($fetcher->fetchAndCache());
    }
}
