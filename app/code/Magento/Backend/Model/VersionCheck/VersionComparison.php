<?php
declare(strict_types=1);

namespace Magento\Backend\Model\VersionCheck;

use Composer\Semver\Comparator;

class VersionComparison
{
    public function __construct(
        private readonly LatestVersionFetcher $fetcher,
        private readonly SystemPackageResolver $packageResolver
    ) {
    }

    public function isUpdateAvailable(): bool
    {
        $latest = $this->fetcher->getLatestVersion();
        $current = $this->getCurrentVersion();

        if ($latest === null || $current === null) {
            return false;
        }

        return Comparator::greaterThan($latest, $current);
    }

    public function isMajorOrMinorUpdate(): bool
    {
        $latest = $this->fetcher->getLatestVersion();
        $current = $this->getCurrentVersion();

        if ($latest === null || $current === null) {
            return false;
        }

        if (!Comparator::greaterThan($latest, $current)) {
            return false;
        }

        $currentParts = explode('.', $current);
        $latestParts = explode('.', $latest);

        return ($latestParts[0] ?? '0') !== ($currentParts[0] ?? '0')
            || ($latestParts[1] ?? '0') !== ($currentParts[1] ?? '0');
    }

    public function getCurrentVersion(): ?string
    {
        return $this->packageResolver->getInstalledVersion();
    }

    public function getLatestVersion(): ?string
    {
        return $this->fetcher->getLatestVersion();
    }
}
