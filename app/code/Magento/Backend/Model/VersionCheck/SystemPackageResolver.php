<?php
declare(strict_types=1);

namespace Magento\Backend\Model\VersionCheck;

use Magento\Framework\Composer\ComposerInformation;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * Resolves the primary system package name and version from composer metadata.
 */
class SystemPackageResolver
{
    /**
     * @var array|null
     */
    private ?array $resolvedPackage = null;

    /**
     * @var bool
     */
    private bool $resolved = false;

    /**
     * @param ComposerInformation $composerInformation
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly ComposerInformation $composerInformation,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * Get the system package name
     *
     * @return string|null
     */
    public function getPackageName(): ?string
    {
        return $this->resolve()['name'] ?? null;
    }

    /**
     * Get the installed version of the system package
     *
     * @return string|null
     */
    public function getInstalledVersion(): ?string
    {
        return $this->resolve()['version'] ?? null;
    }

    /**
     * Resolve system package data, caching the result
     *
     * @return array|null
     */
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
