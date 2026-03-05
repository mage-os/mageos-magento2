<?php
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Model\VersionCheck;

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
    private ClientInterface|MockObject $httpClient;
    private CacheInterface|MockObject $cache;
    private LoggerInterface|MockObject $logger;
    private SystemPackageResolver|MockObject $packageResolver;
    private ComposerInformation|MockObject $composerInformation;
    private ScopeConfigInterface|MockObject $scopeConfig;
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

        $this->fetcher = new LatestVersionFetcher(
            $this->httpClient,
            $this->cache,
            $this->logger,
            $this->packageResolver,
            $this->composerInformation,
            $this->scopeConfig
        );
    }

    public function testReturnsCachedVersion(): void
    {
        $this->packageResolver->method('getPackageName')
            ->willReturn('mage-os/product-community-edition');

        $expectedCacheKey = LatestVersionFetcher::CACHE_KEY_PREFIX . 'mage-os_product-community-edition';
        $this->cache->method('load')
            ->with($expectedCacheKey)
            ->willReturn('2.1.0');

        $this->httpClient->expects($this->never())->method('get');

        $this->assertSame('2.1.0', $this->fetcher->getLatestVersion());
    }

    public function testFetchesAndCachesVersionFromDiscoveredRepo(): void
    {
        $this->cache->method('load')->willReturn(false);
        $this->packageResolver->method('getPackageName')
            ->willReturn('mage-os/product-community-edition');

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

        $expectedCacheKey = LatestVersionFetcher::CACHE_KEY_PREFIX . 'mage-os_product-community-edition';
        $this->cache->expects($this->once())
            ->method('save')
            ->with('2.1.0', $expectedCacheKey, [], 86400);

        $this->assertSame('2.1.0', $this->fetcher->getLatestVersion());
    }

    public function testTriesMultipleReposAndUsesFirstSuccessful(): void
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
            $this->scopeConfig
        );

        $this->cache->method('load')->willReturn(false);
        $this->packageResolver->method('getPackageName')
            ->willReturn('mage-os/product-community-edition');

        $responseBody = json_encode([
            'packages' => [
                'mage-os/product-community-edition' => [
                    ['version' => '3.0.0'],
                ],
            ],
        ]);

        $statusCallCount = 0;
        $this->httpClient->method('getStatus')->willReturnCallback(function () use (&$statusCallCount) {
            $statusCallCount++;
            return $statusCallCount === 1 ? 404 : 200;
        });
        $this->httpClient->method('getBody')->willReturn($responseBody);

        $this->assertSame('3.0.0', $fetcher->getLatestVersion());
    }

    public function testReturnsNullWhenDisabled(): void
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
            $scopeConfig
        );

        $this->httpClient->expects($this->never())->method('get');
        $this->assertNull($fetcher->getLatestVersion());
    }

    public function testReturnsNullWhenNoSystemPackageInstalled(): void
    {
        $this->cache->method('load')->willReturn(false);
        $this->packageResolver->method('getPackageName')->willReturn(null);

        $this->httpClient->expects($this->never())->method('get');

        $this->assertNull($this->fetcher->getLatestVersion());
    }

    public function testReturnsNullWhenAllReposFail(): void
    {
        $this->cache->method('load')->willReturn(false);
        $this->packageResolver->method('getPackageName')
            ->willReturn('mage-os/product-community-edition');

        $this->httpClient->method('getStatus')->willReturn(500);

        $this->assertNull($this->fetcher->getLatestVersion());
    }

    public function testReturnsNullOnEmptyPackageList(): void
    {
        $this->cache->method('load')->willReturn(false);
        $this->packageResolver->method('getPackageName')
            ->willReturn('mage-os/product-community-edition');

        $responseBody = json_encode(['packages' => ['mage-os/product-community-edition' => []]]);
        $this->httpClient->method('getStatus')->willReturn(200);
        $this->httpClient->method('getBody')->willReturn($responseBody);

        $this->assertNull($this->fetcher->getLatestVersion());
    }

    public function testReturnsNullWhenNoReposConfigured(): void
    {
        $composerInformation = $this->createMock(ComposerInformation::class);
        $composerInformation->method('getRootRepositories')->willReturn([]);

        $fetcher = new LatestVersionFetcher(
            $this->httpClient,
            $this->cache,
            $this->logger,
            $this->packageResolver,
            $composerInformation,
            $this->scopeConfig
        );

        $this->cache->method('load')->willReturn(false);
        $this->packageResolver->method('getPackageName')
            ->willReturn('mage-os/product-community-edition');

        $this->httpClient->expects($this->never())->method('get');
        $this->assertNull($fetcher->getLatestVersion());
    }

    public function testFiltersOutNonStableVersions(): void
    {
        $this->cache->method('load')->willReturn(false);
        $this->packageResolver->method('getPackageName')
            ->willReturn('mage-os/product-community-edition');

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

        $this->assertSame('2.1.0', $this->fetcher->getLatestVersion());
    }

    public function testFindsHighestVersionRegardlessOfOrder(): void
    {
        $this->cache->method('load')->willReturn(false);
        $this->packageResolver->method('getPackageName')
            ->willReturn('mage-os/product-community-edition');

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

        $this->assertSame('2.1.0', $this->fetcher->getLatestVersion());
    }
}
