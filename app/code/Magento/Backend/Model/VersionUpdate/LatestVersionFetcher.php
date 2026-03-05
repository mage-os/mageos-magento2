<?php
declare(strict_types=1);

namespace Magento\Backend\Model\VersionUpdate;

use Composer\Semver\VersionParser;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\HTTP\ClientInterface;
use Psr\Log\LoggerInterface;

class LatestVersionFetcher
{
    public const CACHE_KEY = 'mageos_latest_version';
    public const CACHE_LIFETIME = 86400; // 24 hours

    public function __construct(
        private readonly ClientInterface $httpClient,
        private readonly CacheInterface $cache,
        private readonly LoggerInterface $logger,
        private readonly SystemPackageResolver $packageResolver,
        private readonly string $repoBaseUrl = 'https://repo.mage-os.org'
    ) {
    }

    public function getLatestVersion(): ?string
    {
        $cached = $this->cache->load(self::CACHE_KEY);
        if ($cached !== false) {
            return $cached;
        }

        $packageName = $this->packageResolver->getPackageName();
        if ($packageName === null) {
            return null;
        }

        try {
            $url = sprintf('%s/p2/%s.json', $this->repoBaseUrl, $packageName);
            $this->httpClient->get($url);

            if ($this->httpClient->getStatus() !== 200) {
                return null;
            }

            $data = json_decode($this->httpClient->getBody(), true);
            $versions = $data['packages'][$packageName] ?? [];

            $latestStable = $this->findLatestStable($versions);
            if ($latestStable === null) {
                return null;
            }

            $this->cache->save($latestStable, self::CACHE_KEY, [], self::CACHE_LIFETIME);

            return $latestStable;
        } catch (\Exception $e) {
            $this->logger->warning('Failed to fetch latest Mage-OS version: ' . $e->getMessage());
            return null;
        }
    }

    private function findLatestStable(array $versions): ?string
    {
        $parser = new VersionParser();
        $latest = null;

        foreach ($versions as $entry) {
            $version = $entry['version'] ?? '';
            try {
                $normalized = $parser->normalize($version);
                if (preg_match('/(dev|alpha|beta|rc|patch)/i', $normalized)) {
                    continue;
                }
                $latest = $version;
            } catch (\UnexpectedValueException $e) {
                continue;
            }
        }

        return $latest;
    }
}
