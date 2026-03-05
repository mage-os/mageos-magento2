<?php
declare(strict_types=1);

namespace Magento\Backend\Model\VersionCheck;

use Magento\Framework\Composer\ComposerInformation;

class SystemPackageResolver
{
    private ?array $resolvedPackage = null;
    private bool $resolved = false;

    public function __construct(
        private readonly ComposerInformation $composerInformation
    ) {
    }

    public function getPackageName(): ?string
    {
        return $this->resolve()['name'] ?? null;
    }

    public function getInstalledVersion(): ?string
    {
        return $this->resolve()['version'] ?? null;
    }

    private function resolve(): ?array
    {
        if (!$this->resolved) {
            $packages = $this->composerInformation->getSystemPackages();
            $this->resolvedPackage = !empty($packages) ? reset($packages) : null;
            $this->resolved = true;
        }

        return $this->resolvedPackage;
    }
}
