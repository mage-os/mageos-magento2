<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Writer;

use MageOS\Installer\Model\InstallationContext;

/**
 * Manages installation configuration file for resume capability
 */
class ConfigFileManager
{
    private const CONFIG_FILE = '.mageos-install-config.json';

    /**
     * Save configuration to file (from InstallationContext)
     *
     * @param string $baseDir
     * @param InstallationContext $context
     * @return bool
     */
    public function saveContext(string $baseDir, InstallationContext $context): bool
    {
        $configFile = $baseDir . '/' . self::CONFIG_FILE;

        // Serialize context (automatically excludes passwords)
        $config = $context->toArray();

        // Add metadata
        $configWithMeta = [
            '_metadata' => [
                'created_at' => $config['_created_at'] ?? date('Y-m-d H:i:s'),
                'version' => '1.0.0',
                'note' => 'This file contains your installation configuration (passwords excluded). You can delete it after successful installation.',
                'sensitive_fields_excluded' => $context->getSensitiveFields()
            ],
            'config' => $config
        ];

        $json = json_encode($configWithMeta, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        if ($json === false) {
            return false;
        }

        $result = file_put_contents($configFile, $json);

        if ($result !== false) {
            // Set proper permissions (readable only by owner)
            chmod($configFile, 0600);
        }

        return $result !== false;
    }

    /**
     * Load configuration from file as InstallationContext
     *
     * @param string $baseDir
     * @return InstallationContext|null
     */
    public function loadContext(string $baseDir): ?InstallationContext
    {
        $configFile = $baseDir . '/' . self::CONFIG_FILE;

        if (!file_exists($configFile)) {
            return null;
        }

        $content = file_get_contents($configFile);

        if ($content === false) {
            return null;
        }

        $data = json_decode($content, true);

        if (!is_array($data) || !isset($data['config'])) {
            return null;
        }

        // Deserialize to context
        return InstallationContext::fromArray($data['config']);
    }

    /**
     * Save configuration to file (legacy array-based method)
     *
     * @deprecated Use saveContext() instead
     * @param string $baseDir
     * @param array<string, mixed> $config
     * @return bool
     */
    public function save(string $baseDir, array $config): bool
    {
        $configFile = $baseDir . '/' . self::CONFIG_FILE;

        // Add metadata
        $configWithMeta = [
            '_metadata' => [
                'created_at' => date('Y-m-d H:i:s'),
                'version' => '1.0.0',
                'note' => 'This file contains your installation configuration. You can delete it after successful installation.'
            ],
            'config' => $config
        ];

        $json = json_encode($configWithMeta, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        if ($json === false) {
            return false;
        }

        $result = file_put_contents($configFile, $json);

        if ($result !== false) {
            // Set proper permissions (readable only by owner)
            chmod($configFile, 0600);
        }

        return $result !== false;
    }

    /**
     * Load configuration from file (legacy array-based method)
     *
     * @deprecated Use loadContext() instead
     * @param string $baseDir
     * @return array<string, mixed>|null
     */
    public function load(string $baseDir): ?array
    {
        $configFile = $baseDir . '/' . self::CONFIG_FILE;

        if (!file_exists($configFile)) {
            return null;
        }

        $content = file_get_contents($configFile);

        if ($content === false) {
            return null;
        }

        $data = json_decode($content, true);

        if (!is_array($data) || !isset($data['config'])) {
            return null;
        }

        return $data['config'];
    }

    /**
     * Check if config file exists
     *
     * @param string $baseDir
     * @return bool
     */
    public function exists(string $baseDir): bool
    {
        $configFile = $baseDir . '/' . self::CONFIG_FILE;
        return file_exists($configFile);
    }

    /**
     * Delete config file
     *
     * @param string $baseDir
     * @return bool
     */
    public function delete(string $baseDir): bool
    {
        $configFile = $baseDir . '/' . self::CONFIG_FILE;

        if (!file_exists($configFile)) {
            return true;
        }

        // phpcs:ignore Magento2.Functions.DiscouragedFunction
        return @unlink($configFile);
    }

    /**
     * Get config file path
     *
     * @param string $baseDir
     * @return string
     */
    public function getConfigFilePath(string $baseDir): string
    {
        return $baseDir . '/' . self::CONFIG_FILE;
    }
}
