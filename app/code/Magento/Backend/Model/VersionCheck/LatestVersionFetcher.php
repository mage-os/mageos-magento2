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
    private const CACHE_JITTER = 7200;
    private const NEGATIVE_CACHE_LIFETIME = 300;
    private const HTTP_TIMEOUT = 15;
    private const METADATA_URL_PATTERN = '%s/p2/%s.json';

    public const XML_PATH_ENABLED = 'system/version_check/enabled';
    public const XML_PATH_CACHE_LIFETIME = 'system/version_check/cache_lifetime';

    /**
     * @param ClientInterface $httpClient
     * @param CacheInterface $cache
     * @param LoggerInterface $logger
     * @param SystemPackageResolver $packageResolver
     * @param ComposerInformation $composerInformation
     * @param ScopeConfigInterface $scopeConfig
     * @param VersionParser $versionParser
     */
    public function __construct(
        private readonly ClientInterface $httpClient,
        private readonly CacheInterface $cache,
        private readonly LoggerInterface $logger,
        private readonly SystemPackageResolver $packageResolver,
        private readonly ComposerInformation $composerInformation,
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly VersionParser $versionParser
    ) {
    }

    /**
     * Read-only: returns cached latest version or null if cache is cold/empty
     *
     * Never makes HTTP calls — safe for use during page render.
     *
     * @return string|null
     */
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

        if ($cached === false) {
            return null;
        }

        if ($cached === '') {
            return null;
        }

        return $cached;
    }

    /**
     * Fetch latest version via HTTP and write to cache
     *
     * Called by cron — skips HTTP if cache is already warm.
     *
     * @return string|null
     */
    public function fetchAndCache(): ?string
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
            return $cached !== '' ? $cached : null;
        }

        $latestStable = null;
        try {
            $repoUrls = $this->composerInformation->getRootRepositories();

            foreach ($repoUrls as $repoUrl) {
                if (empty($repoUrl) || !filter_var($repoUrl, FILTER_VALIDATE_URL)) {
                    $this->logger->debug(
                        sprintf('Version check: skipping invalid repository URL: %s', $repoUrl ?? 'null')
                    );
                    continue;
                }

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
        } catch (\Exception $e) {
            $this->logger->warning(
                'Unexpected error during version check',
                ['exception' => $e]
            );
        }

        try {
            if ($latestStable !== null) {
                $baseTtl = (int) $this->scopeConfig->getValue(self::XML_PATH_CACHE_LIFETIME)
                    ?: self::CACHE_LIFETIME;
                $ttl = max(60, $baseTtl + random_int(-self::CACHE_JITTER, self::CACHE_JITTER));
                $this->cache->save($latestStable, $cacheKey, [], $ttl);
            } else {
                $this->cache->save('', $cacheKey, [], self::NEGATIVE_CACHE_LIFETIME);
            }
        } catch (\Exception $e) {
            $this->logger->warning('Failed to cache version check result: ' . $e->getMessage());
        }

        return $latestStable;
    }

    /**
     * Find the latest stable version from a list of package version entries
     *
     * @param array $versions
     * @return string|null
     */
    private function findLatestStable(array $versions): ?string
    {
        $parser = $this->versionParser;
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
