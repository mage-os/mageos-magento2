<?php
declare(strict_types=1);

namespace Magento\Backend\Model\VersionCheck;

/**
 * Compares current installed version against the latest available version.
 */
interface VersionComparisonInterface
{
    /**
     * Check if a newer version is available
     */
    public function isUpdateAvailable(): bool;

    /**
     * Check if the update is a major or minor version bump
     */
    public function isMajorOrMinorUpdate(): bool;

    /**
     * Get the currently installed version
     */
    public function getCurrentVersion(): ?string;

    /**
     * Get the latest available version
     */
    public function getLatestVersion(): ?string;
}
