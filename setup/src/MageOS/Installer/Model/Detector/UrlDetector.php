<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Detector;

/**
 * Detects base URL from environment
 */
class UrlDetector
{
    /**
     * Detect base URL from environment and directory
     *
     * @param string $baseDir
     * @return string|null
     */
    public function detect(string $baseDir): ?string
    {
        // Try to get from environment variables first
        $envUrl = $this->detectFromEnvironment();
        if ($envUrl) {
            return $envUrl;
        }

        // Fall back to directory-based detection
        return $this->detectFromDirectory($baseDir);
    }

    /**
     * Detect URL from environment variables
     *
     * @return string|null
     */
    private function detectFromEnvironment(): ?string
    {
        // Common environment variable names
        $envVars = [
            'BASE_URL',
            'APP_URL',
            'MAGENTO_BASE_URL',
            'URL'
        ];

        foreach ($envVars as $var) {
            $value = getenv($var);
            if ($value && is_string($value)) {
                return rtrim($value, '/') . '/';
            }
        }

        return null;
    }

    /**
     * Detect URL from directory name
     *
     * @param string $baseDir
     * @return string
     */
    private function detectFromDirectory(string $baseDir): string
    {
        $dirName = basename($baseDir);

        // Common patterns for local development
        $patterns = [
            '.test',
            '.local',
            '.localhost'
        ];

        foreach ($patterns as $pattern) {
            if (str_ends_with($dirName, str_replace('.', '', $pattern))) {
                return 'http://' . $dirName . '/';
            }
        }

        // Default pattern: [directory].test
        return 'http://' . $dirName . '.test/';
    }
}
