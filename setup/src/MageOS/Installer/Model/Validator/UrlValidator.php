<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Validator;

/**
 * Validates URLs
 */
class UrlValidator
{
    /**
     * Normalize URL to proper format
     *
     * @param string $url
     * @return array{normalized: string, changed: bool, changes: array<string>}
     */
    public function normalize(string $url): array
    {
        $original = $url;
        $changes = [];

        // Add scheme if missing
        if (!preg_match('/^https?:\/\//', $url)) {
            $url = 'http://' . $url;
            $changes[] = 'Added http:// prefix';
        }

        // Add trailing slash if missing
        if (!str_ends_with($url, '/')) {
            $url = $url . '/';
            $changes[] = 'Added trailing /';
        }

        return [
            'normalized' => $url,
            'changed' => $original !== $url,
            'changes' => $changes
        ];
    }

    /**
     * Validate URL format
     *
     * @param string $url
     * @return array{valid: bool, error: string|null, warning: string|null}
     */
    public function validate(string $url): array
    {
        if (empty($url)) {
            return [
                'valid' => false,
                'error' => 'URL cannot be empty',
                'warning' => null
            ];
        }

        // Normalize first
        $normalized = $this->normalize($url);
        $url = $normalized['normalized'];

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return [
                'valid' => false,
                'error' => 'Invalid URL format',
                'warning' => null
            ];
        }

        // Check if using HTTPS
        $warning = null;
        if (str_starts_with($url, 'http://')) {
            $warning = 'Using HTTP instead of HTTPS. Consider using HTTPS for production environments.';
        }

        return [
            'valid' => true,
            'error' => null,
            'warning' => $warning
        ];
    }

    /**
     * Validate admin path
     *
     * @param string $path
     * @return array{valid: bool, error: string|null, warning: string|null}
     */
    public function validateAdminPath(string $path): array
    {
        if (empty($path)) {
            return [
                'valid' => false,
                'error' => 'Admin path cannot be empty',
                'warning' => null
            ];
        }

        // Check for valid characters
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $path)) {
            return [
                'valid' => false,
                'error' => 'Admin path can only contain letters, numbers, underscores, and hyphens',
                'warning' => null
            ];
        }

        // Warn if using default 'admin'
        $warning = null;
        if ($path === 'admin') {
            $warning = 'Using default "admin" path is not recommended for security. Consider using a custom path.';
        }

        return [
            'valid' => true,
            'error' => null,
            'warning' => $warning
        ];
    }
}
