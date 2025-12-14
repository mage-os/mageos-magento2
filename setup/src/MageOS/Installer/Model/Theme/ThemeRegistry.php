<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Theme;

/**
 * Registry of installable themes
 */
class ThemeRegistry
{
    public const THEME_LUMA = 'luma';
    public const THEME_BLANK = 'blank';
    public const THEME_HYVA = 'hyva';

    /**
     * Get list of available themes
     *
     * @return array<string, array{
     *     name: string,
     *     description: string,
     *     package: string|null,
     *     requires_auth: bool,
     *     is_default: bool
     * }>
     */
    public function getAvailableThemes(): array
    {
        return [
            self::THEME_LUMA => [
                'name' => 'Luma',
                'description' => 'Default Magento theme (already installed)',
                'package' => null,
                'requires_auth' => false,
                'is_default' => true
            ],
            self::THEME_BLANK => [
                'name' => 'Blank',
                'description' => 'Minimal Magento theme (already installed)',
                'package' => null,
                'requires_auth' => false,
                'is_default' => true
            ],
            self::THEME_HYVA => [
                'name' => 'Hyva',
                'description' => 'Modern, performance-focused theme (open source)',
                'package' => 'hyva-themes/magento2-default-theme',
                'requires_auth' => true,
                'is_default' => false
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
     *     is_default: bool
     * }|null
     */
    public function getTheme(string $themeId): ?array
    {
        $themes = $this->getAvailableThemes();
        return $themes[$themeId] ?? null;
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
     * Check if theme is already installed (default)
     *
     * @param string $themeId
     * @return bool
     */
    public function isDefaultTheme(string $themeId): bool
    {
        $theme = $this->getTheme($themeId);
        return $theme ? $theme['is_default'] : false;
    }
}
