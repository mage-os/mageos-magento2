<?php
declare(strict_types=1);

namespace Magento\Backend\Model\VersionCheck;

use Composer\Semver\Comparator;
use Composer\Semver\VersionParser;
use Psr\Log\LoggerInterface;
use UnexpectedValueException;

class VersionComparison implements VersionComparisonInterface
{
    private bool $resolved = false;
    private ?string $latestVersion = null;
    private ?string $currentVersion = null;

    public function __construct(
        private readonly LatestVersionFetcher $fetcher,
        private readonly SystemPackageResolver $packageResolver,
        private readonly VersionParser $versionParser,
        private readonly LoggerInterface $logger
    ) {
    }

    public function isUpdateAvailable(): bool
    {
        $this->resolve();

        if ($this->latestVersion === null || $this->currentVersion === null) {
            return false;
        }

        return Comparator::greaterThan($this->latestVersion, $this->currentVersion);
    }

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

    public function getCurrentVersion(): ?string
    {
        $this->resolve();
        return $this->currentVersion;
    }

    public function getLatestVersion(): ?string
    {
        $this->resolve();
        return $this->latestVersion;
    }

    private function resolve(): void
    {
        if (!$this->resolved) {
            $this->latestVersion = $this->fetcher->getLatestVersion();
            $this->currentVersion = $this->packageResolver->getInstalledVersion();
            $this->resolved = true;
        }
    }
}
