<?php
declare(strict_types=1);

namespace Magento\Backend\Model\VersionCheck;

use Magento\Framework\Composer\ComposerInformation;
use Psr\Log\LoggerInterface;
use Throwable;

class SystemPackageResolver
{
    private ?array $resolvedPackage = null;
    private bool $resolved = false;

    public function __construct(
        private readonly ComposerInformation $composerInformation,
        private readonly LoggerInterface $logger
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
            try {
                $packages = $this->composerInformation->getSystemPackages();
                $this->resolvedPackage = !empty($packages) ? reset($packages) : null;
            } catch (Throwable $e) {
                $this->logger->warning(
                    'Failed to resolve system packages for version check',
                    ['exception' => $e]
                );
                $this->resolvedPackage = null;
            } finally {
                $this->resolved = true;
            }
        }

        return $this->resolvedPackage;
    }
}
