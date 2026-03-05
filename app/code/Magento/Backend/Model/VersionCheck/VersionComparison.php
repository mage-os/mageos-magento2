<?php
declare(strict_types=1);

namespace Magento\Backend\Model\VersionCheck;

use Composer\Semver\Comparator;
use Magento\Backend\Api\VersionComparisonInterface;

class VersionComparison implements VersionComparisonInterface
{
    private bool $resolved = false;
    private ?string $latestVersion = null;
    private ?string $currentVersion = null;

    public function __construct(
        private readonly LatestVersionFetcher $fetcher,
        private readonly SystemPackageResolver $packageResolver
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

        $currentParts = explode('.', $this->currentVersion);
        $latestParts = explode('.', $this->latestVersion);

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
