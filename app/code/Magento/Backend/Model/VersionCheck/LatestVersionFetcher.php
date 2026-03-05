<?php
declare(strict_types=1);

namespace Magento\Backend\Model\VersionCheck;

use Composer\Semver\Comparator;
use Composer\Semver\VersionParser;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Composer\ComposerInformation;
use Magento\Framework\HTTP\ClientInterface;
use Psr\Log\LoggerInterface;

class LatestVersionFetcher
{
    public const CACHE_KEY_PREFIX = 'distro_latest_version_';
    public const CACHE_LIFETIME = 86400;
    public const XML_PATH_ENABLED = 'system/version_check/enabled';
    public const XML_PATH_CACHE_LIFETIME = 'system/version_check/cache_lifetime';
    private const METADATA_URL_PATTERN = '%s/p2/%s.json';

    public function __construct(
        private readonly ClientInterface $httpClient,
        private readonly CacheInterface $cache,
        private readonly LoggerInterface $logger,
        private readonly SystemPackageResolver $packageResolver,
        private readonly ComposerInformation $composerInformation,
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    public function getLatestVersion(): ?string
    {
        if (!$this->scopeConfig->isSetFlag(self::XML_PATH_ENABLED)) {
            return null;
        }

        $packageName = $this->packageResolver->getPackageName();
        if ($packageName === null) {
            return null;
        }

        $cacheKey = self::CACHE_KEY_PREFIX . str_replace('/', '_', $packageName);
        $cached = $this->cache->load($cacheKey);
        if ($cached !== false) {
            return $cached;
        }

        try {
            $repoUrls = $this->composerInformation->getRootRepositories();
            $latestStable = null;

            foreach ($repoUrls as $repoUrl) {
                $url = sprintf(self::METADATA_URL_PATTERN, rtrim($repoUrl, '/'), $packageName);
                $this->httpClient->get($url);

                if ($this->httpClient->getStatus() !== 200) {
                    continue;
                }

                $data = json_decode($this->httpClient->getBody(), true);
                $versions = $data['packages'][$packageName] ?? [];
                $latestStable = $this->findLatestStable($versions);

                if ($latestStable !== null) {
                    break;
                }
            }

            if ($latestStable === null) {
                return null;
            }

            $cacheLifetime = (int) $this->scopeConfig->getValue(self::XML_PATH_CACHE_LIFETIME)
                ?: self::CACHE_LIFETIME;
            $this->cache->save($latestStable, $cacheKey, [], $cacheLifetime);

            return $latestStable;
        } catch (\Exception $e) {
            $this->logger->warning('Failed to fetch latest distribution version: ' . $e->getMessage());
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
                if (preg_match('/(dev|alpha|beta|RC)/i', $normalized)) {
                    continue;
                }
                if ($latest === null || Comparator::greaterThan($version, $latest)) {
                    $latest = $version;
                }
            } catch (\UnexpectedValueException $e) {
                continue;
            }
        }

        return $latest;
    }
}
