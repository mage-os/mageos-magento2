<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Theme;

/**
 * Registry of installable themes
 *
 * To add a custom theme:
 * 1. Add a new constant: public const THEME_YOURTHEME = 'yourtheme';
 * 2. Add to getAvailableThemes() array with theme metadata
 * 3. Implement installation logic in ThemeInstaller
 * 4. Optionally create a dedicated installer class (like HyvaInstaller)
 */
class ThemeRegistry
{
    public const THEME_HYVA = 'hyva';
    public const THEME_LUMA = 'luma';

    /**
     * Get list of available themes
     *
     * @return array<string, array{
     *     name: string,
     *     description: string,
     *     package: string|null,
     *     requires_auth: bool,
     *     is_already_installed: bool,
     *     is_recommended: bool,
     *     sort_order: int
     * }>
     */
    public function getAvailableThemes(): array
    {
        return [
            self::THEME_HYVA => [
                'name' => 'Hyva',
                'description' => 'Modern, performance-focused theme (recommended)',
                'package' => 'hyva-themes/magento2-default-theme',
                'requires_auth' => true,
                'is_already_installed' => false,
                'is_recommended' => true,
                'sort_order' => 1
            ],
            self::THEME_LUMA => [
                'name' => 'Luma',
                'description' => 'Legacy Magento theme (already installed)',
                'package' => null,
                'requires_auth' => false,
                'is_already_installed' => true,
                'is_recommended' => false,
                'sort_order' => 2
            ]
        ];
    }

    /**
     * Get theme by ID
     *
     * @param string $themeId
     * @return array{
     *     name: string,
     *     description: string,
     *     package: string|null,
     *     requires_auth: bool,
     *     is_already_installed: bool,
     *     is_recommended: bool,
     *     sort_order: int
     * }|null
     */
    public function getTheme(string $themeId): ?array
    {
        $themes = $this->getAvailableThemes();
        return $themes[$themeId] ?? null;
    }

    /**
     * Get recommended (default) theme ID
     *
     * @return string
     */
    public function getRecommendedThemeId(): string
    {
        foreach ($this->getAvailableThemes() as $themeId => $theme) {
            if ($theme['is_recommended']) {
                return $themeId;
            }
        }

        return self::THEME_HYVA; // Fallback to Hyva
    }

    /**
     * Check if theme requires authentication
     *
     * @param string $themeId
     * @return bool
     */
    public function requiresAuth(string $themeId): bool
    {
        $theme = $this->getTheme($themeId);
        return $theme ? $theme['requires_auth'] : false;
    }

    /**
     * Check if theme is already installed
     *
     * @param string $themeId
     * @return bool
     */
    public function isAlreadyInstalled(string $themeId): bool
    {
        $theme = $this->getTheme($themeId);
        return $theme ? $theme['is_already_installed'] : false;
    }
}
