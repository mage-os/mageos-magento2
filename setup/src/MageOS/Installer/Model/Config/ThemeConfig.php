<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Config;

use MageOS\Installer\Model\Theme\ThemeRegistry;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\note;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

/**
 * Collects theme configuration with Laravel Prompts
 */
class ThemeConfig
{
    public function __construct(
        private readonly ThemeRegistry $themeRegistry
    ) {
    }

    /**
     * Collect theme configuration
     *
     * @return array{
     *     install: bool,
     *     theme: string|null,
     *     hyva_project_key: string|null,
     *     hyva_api_token: string|null
     * }
     */
    public function collect(): array
    {
        // Ask if user wants to install a theme
        $installTheme = confirm(
            label: 'Install a theme?',
            default: true,
            hint: 'Hyva recommended for best performance and modern UX'
        );

        if (!$installTheme) {
            info('Skipping theme installation (Luma will be used)');
            return [
                'install' => false,
                'theme' => ThemeRegistry::THEME_LUMA,
                'hyva_project_key' => null,
                'hyva_api_token' => null
            ];
        }

        // Get available themes
        $themes = $this->themeRegistry->getAvailableThemes();
        uasort($themes, fn($a, $b) => $a['sort_order'] <=> $b['sort_order']);

        // Build options for select
        $options = [];
        $defaultTheme = ThemeRegistry::THEME_HYVA;

        foreach ($themes as $themeId => $themeInfo) {
            $options[$themeId] = sprintf('%s - %s', $themeInfo['name'], $themeInfo['description']);
        }

        // Select theme
        $themeId = select(
            label: 'Select theme',
            options: $options,
            default: $defaultTheme,
            hint: 'Use arrow keys to select, Enter to confirm'
        );

        // If already installed (Luma), we're done
        if ($this->themeRegistry->isAlreadyInstalled($themeId)) {
            info(sprintf('✓ Using %s theme (already installed)', $themes[$themeId]['name']));
            return [
                'install' => false,
                'theme' => $themeId,
                'hyva_project_key' => null,
                'hyva_api_token' => null
            ];
        }

        // For Hyva, collect credentials
        if ($themeId === ThemeRegistry::THEME_HYVA) {
            return $this->collectHyvaCredentials($themeId);
        }

        return [
            'install' => true,
            'theme' => $themeId,
            'hyva_project_key' => null,
            'hyva_api_token' => null
        ];
    }

    /**
     * Collect Hyva-specific credentials
     *
     * @param string $themeId
     * @return array{
     *     install: bool,
     *     theme: string,
     *     hyva_project_key: string,
     *     hyva_api_token: string
     * }
     */
    private function collectHyvaCredentials(string $themeId): array
    {
        note('Hyva Theme Credentials');

        info('Hyva requires API credentials from your account');
        info('Get your credentials at: https://www.hyva.io/hyva-theme-license.html');

        // Project key
        $projectKey = text(
            label: 'Hyva project key',
            placeholder: 'your-project-key',
            hint: 'Found in your Hyva account dashboard',
            validate: fn (string $value) => empty($value)
                ? 'Project key is required for Hyva installation'
                : null
        );

        // API token
        $apiToken = text(
            label: 'Hyva API token',
            placeholder: 'your-api-token',
            hint: 'Found in your Hyva account dashboard',
            validate: fn (string $value) => empty($value)
                ? 'API token is required for Hyva installation'
                : null
        );

        return [
            'install' => true,
            'theme' => $themeId,
            'hyva_project_key' => $projectKey,
            'hyva_api_token' => $apiToken
        ];
    }
}
