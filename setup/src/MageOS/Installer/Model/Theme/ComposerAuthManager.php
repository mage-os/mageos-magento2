<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Theme;

/**
 * Manages Composer authentication configuration
 */
class ComposerAuthManager
{
    /**
     * Add Hyva authentication to auth.json
     *
     * @param string $baseDir
     * @param string $projectKey
     * @param string $apiToken
     * @return void
     * @throws \RuntimeException
     */
    public function addHyvaAuth(string $baseDir, string $projectKey, string $apiToken): void
    {
        $authFile = $baseDir . '/auth.json';

        // Load existing auth.json or create new structure
        if (file_exists($authFile)) {
            $authData = json_decode(file_get_contents($authFile), true);
            if (!is_array($authData)) {
                $authData = [];
            }
        } else {
            $authData = [];
        }

        // Ensure http-basic structure exists
        if (!isset($authData['http-basic'])) {
            $authData['http-basic'] = [];
        }

        // Add Hyva credentials (project key as username, API token as password)
        $authData['http-basic']['hyva-themes.repo.packagist.com'] = [
            'username' => $projectKey,
            'password' => $apiToken
        ];

        // Write back to auth.json
        $json = json_encode($authData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        if ($json === false) {
            throw new \RuntimeException('Failed to encode auth.json');
        }

        if (file_put_contents($authFile, $json) === false) {
            throw new \RuntimeException('Failed to write auth.json');
        }

        // Set proper permissions
        chmod($authFile, 0600);
    }

    /**
     * Add Hyva repository to composer.json
     *
     * @param string $baseDir
     * @param string $projectKey
     * @return void
     * @throws \RuntimeException
     */
    public function addHyvaRepository(string $baseDir, string $projectKey): void
    {
        $composerFile = $baseDir . '/composer.json';

        if (!file_exists($composerFile)) {
            throw new \RuntimeException('composer.json not found');
        }

        $composerData = json_decode(file_get_contents($composerFile), true);
        if (!is_array($composerData)) {
            throw new \RuntimeException('Invalid composer.json');
        }

        // Ensure repositories structure exists
        if (!isset($composerData['repositories'])) {
            $composerData['repositories'] = [];
        }

        // Add Hyva repository
        $hyvaRepo = [
            'type' => 'composer',
            'url' => sprintf('https://hyva-themes.repo.packagist.com/%s/', $projectKey)
        ];

        // Check if already exists
        $exists = false;
        foreach ($composerData['repositories'] as $repo) {
            if (is_array($repo) &&
                isset($repo['url']) &&
                str_contains($repo['url'], 'hyva-themes.repo.packagist.com')) {
                $exists = true;
                break;
            }
        }

        if (!$exists) {
            $composerData['repositories']['private-packagist'] = $hyvaRepo;
        }

        // Write back to composer.json
        $json = json_encode($composerData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        if ($json === false) {
            throw new \RuntimeException('Failed to encode composer.json');
        }

        if (file_put_contents($composerFile, $json) === false) {
            throw new \RuntimeException('Failed to write composer.json');
        }
    }
}
