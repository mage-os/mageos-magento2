<?php
declare(strict_types=1);

namespace Magento\Backend\Model\VersionCheck;

use Composer\Semver\Comparator;
use Composer\Semver\VersionParser;
use Psr\Log\LoggerInterface;
use UnexpectedValueException;

/**
 * Compares the installed distribution version against the latest available version.
 */
class VersionComparison implements VersionComparisonInterface
{
    /**
     * @var bool
     */
    private bool $resolved = false;

    /**
     * @var string|null
     */
    private ?string $latestVersion = null;

    /**
     * @var string|null
     */
    private ?string $currentVersion = null;

    /**
     * @param LatestVersionFetcher $fetcher
     * @param SystemPackageResolver $packageResolver
     * @param VersionParser $versionParser
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly LatestVersionFetcher $fetcher,
        private readonly SystemPackageResolver $packageResolver,
        private readonly VersionParser $versionParser,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * @inheritdoc
     */
    public function isUpdateAvailable(): bool
    {
        $this->resolve();

        if ($this->latestVersion === null || $this->currentVersion === null) {
            return false;
        }

        return Comparator::greaterThan($this->latestVersion, $this->currentVersion);
    }

    /**
     * @inheritdoc
     */
    public function isMajorOrMinorUpdate(): bool
    {
        if (!$this->isUpdateAvailable()) {
            return false;
        }

        $parser = $this->versionParser;
        try {
            $currentNormalized = $parser->normalize($this->currentVersion);
            $latestNormalized = $parser->normalize($this->latestVersion);
        } catch (UnexpectedValueException $e) {
            $this->logger->warning(
                'Version normalization failed during major/minor comparison',
                [
                    'current' => $this->currentVersion,
                    'latest' => $this->latestVersion,
                    'exception' => $e,
                ]
            );
            return false;
        }

        $currentParts = explode('.', $currentNormalized);
        $latestParts = explode('.', $latestNormalized);

        return ($latestParts[0] ?? '0') !== ($currentParts[0] ?? '0')
            || ($latestParts[1] ?? '0') !== ($currentParts[1] ?? '0');
    }

    /**
     * @inheritdoc
     */
    public function getCurrentVersion(): ?string
    {
        $this->resolve();
        return $this->currentVersion;
    }

    /**
     * @inheritdoc
     */
    public function getLatestVersion(): ?string
    {
        $this->resolve();
        return $this->latestVersion;
    }

    /**
     * Lazy-resolve current and latest versions
     *
     * @return void
     */
    private function resolve(): void
    {
        if (!$this->resolved) {
            $this->latestVersion = $this->fetcher->getLatestVersion();
            $this->currentVersion = $this->packageResolver->getInstalledVersion();
            $this->resolved = true;
        }
    }
}
