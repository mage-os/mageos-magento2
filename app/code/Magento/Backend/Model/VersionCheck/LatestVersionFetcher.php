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
    private const CACHE_KEY_PREFIX = 'distro_latest_version_';
    private const CACHE_LIFETIME = 86400;
    private const HTTP_TIMEOUT = 3;
    private const METADATA_URL_PATTERN = '%s/p2/%s.json';

    public const XML_PATH_ENABLED = 'system/version_check/enabled';
    public const XML_PATH_CACHE_LIFETIME = 'system/version_check/cache_lifetime';

    private ?VersionParser $versionParser = null;

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
            $this->logger->debug('Version check skipped: no system package detected');
            return null;
        }

        $cacheKey = self::CACHE_KEY_PREFIX . str_replace('/', '_', $packageName);
        $cached = $this->cache->load($cacheKey);
        if ($cached !== false) {
            return $cached;
        }

        $latestStable = null;
        try {
            $repoUrls = $this->composerInformation->getRootRepositories();

            foreach ($repoUrls as $repoUrl) {
                $url = sprintf(self::METADATA_URL_PATTERN, rtrim($repoUrl, '/'), $packageName);
                $this->httpClient->setTimeout(self::HTTP_TIMEOUT);
                $this->httpClient->get($url);

                $status = $this->httpClient->getStatus();
                if ($status !== 200) {
                    $this->logger->debug(
                        sprintf('Version check: %s returned HTTP %d', $url, $status)
                    );
                    continue;
                }

                $data = json_decode($this->httpClient->getBody(), true);
                if (!is_array($data)) {
                    $this->logger->info(
                        sprintf('Version check: non-JSON response from %s', $url)
                    );
                    continue;
                }

                $versions = $data['packages'][$packageName] ?? [];
                $latestStable = $this->findLatestStable($versions);

                if ($latestStable !== null) {
                    break;
                }
            }
        } catch (\RuntimeException $e) {
            $this->logger->info('Version check network failure: ' . $e->getMessage());
            return null;
        } catch (\Exception $e) {
            $this->logger->warning(
                'Unexpected error during version check',
                ['exception' => $e]
            );
            return null;
        }

        if ($latestStable === null) {
            return null;
        }

        try {
            $cacheLifetime = (int) $this->scopeConfig->getValue(self::XML_PATH_CACHE_LIFETIME)
                ?: self::CACHE_LIFETIME;
            $this->cache->save($latestStable, $cacheKey, [], $cacheLifetime);
        } catch (\Exception $e) {
            $this->logger->warning('Failed to cache version check result: ' . $e->getMessage());
        }

        return $latestStable;
    }

    private function findLatestStable(array $versions): ?string
    {
        $parser = $this->versionParser ??= new VersionParser();
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
