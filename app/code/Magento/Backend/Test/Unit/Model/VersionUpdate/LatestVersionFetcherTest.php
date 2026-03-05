<?php
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Model\VersionUpdate;

use Magento\Backend\Model\VersionUpdate\LatestVersionFetcher;
use Magento\Backend\Model\VersionUpdate\SystemPackageResolver;
use Magento\Framework\App\CacheInterface;
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
    private LatestVersionFetcher $fetcher;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(ClientInterface::class);
        $this->cache = $this->createMock(CacheInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->packageResolver = $this->createMock(SystemPackageResolver::class);

        $this->fetcher = new LatestVersionFetcher(
            $this->httpClient,
            $this->cache,
            $this->logger,
            $this->packageResolver,
            'https://repo.mage-os.org'
        );
    }

    public function testReturnsCachedVersion(): void
    {
        $this->cache->method('load')
            ->with(LatestVersionFetcher::CACHE_KEY)
            ->willReturn('2.1.0');

        $this->httpClient->expects($this->never())->method('get');

        $this->assertSame('2.1.0', $this->fetcher->getLatestVersion());
    }

    public function testFetchesAndCachesVersionForCommunityEdition(): void
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

        $this->cache->expects($this->once())
            ->method('save')
            ->with('2.1.0', LatestVersionFetcher::CACHE_KEY, [], LatestVersionFetcher::CACHE_LIFETIME);

        $this->assertSame('2.1.0', $this->fetcher->getLatestVersion());
    }

    public function testFetchesVersionForEnterpriseEdition(): void
    {
        $this->cache->method('load')->willReturn(false);
        $this->packageResolver->method('getPackageName')
            ->willReturn('mage-os/product-enterprise-edition');

        $responseBody = json_encode([
            'packages' => [
                'mage-os/product-enterprise-edition' => [
                    ['version' => '3.0.0'],
                ],
            ],
        ]);

        $this->httpClient->expects($this->once())
            ->method('get')
            ->with('https://repo.mage-os.org/p2/mage-os/product-enterprise-edition.json');
        $this->httpClient->method('getStatus')->willReturn(200);
        $this->httpClient->method('getBody')->willReturn($responseBody);

        $this->assertSame('3.0.0', $this->fetcher->getLatestVersion());
    }

    public function testReturnsNullWhenNoSystemPackageInstalled(): void
    {
        $this->cache->method('load')->willReturn(false);
        $this->packageResolver->method('getPackageName')->willReturn(null);

        $this->httpClient->expects($this->never())->method('get');

        $this->assertNull($this->fetcher->getLatestVersion());
    }

    public function testReturnsNullOnHttpFailure(): void
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
}
