<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Theme;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Orchestrates theme installation
 */
class ThemeInstaller
{
    public function __construct(
        private readonly ThemeRegistry $themeRegistry,
        private readonly HyvaInstaller $hyvaInstaller
    ) {
    }

    /**
     * Install theme based on configuration
     *
     * @param string $baseDir
     * @param array{
     *     install: bool,
     *     theme: string|null,
     *     hyva_license_key: string|null,
     *     hyva_project_name: string|null
     * } $themeConfig
     * @param OutputInterface $output
     * @return bool
     */
    public function install(string $baseDir, array $themeConfig, OutputInterface $output): bool
    {
        if (!$themeConfig['install'] || !$themeConfig['theme']) {
            return true; // Nothing to install
        }

        $themeId = $themeConfig['theme'];
        $theme = $this->themeRegistry->getTheme($themeId);

        if (!$theme) {
            $output->writeln('<error>❌ Unknown theme: ' . $themeId . '</error>');
            return false;
        }

        $output->writeln('');
        $output->writeln(sprintf('<comment>🔄 Installing %s theme...</comment>', $theme['name']));

        // Handle Hyva installation
        if ($themeId === ThemeRegistry::THEME_HYVA) {
            return $this->installHyva($baseDir, $themeConfig, $output);
        }

        // For other themes, add installation logic here
        $output->writeln(sprintf('<comment>ℹ️  %s theme installation not yet implemented</comment>', $theme['name']));
        return true;
    }

    /**
     * Install Hyva theme
     *
     * @param string $baseDir
     * @param array{
     *     hyva_license_key: string|null,
     *     hyva_project_name: string|null
     * } $themeConfig
     * @param OutputInterface $output
     * @return bool
     */
    private function installHyva(string $baseDir, array $themeConfig, OutputInterface $output): bool
    {
        if (empty($themeConfig['hyva_license_key']) || empty($themeConfig['hyva_project_name'])) {
            $output->writeln('<error>❌ Hyva credentials are required</error>');
            return false;
        }

        $success = $this->hyvaInstaller->install(
            $baseDir,
            $themeConfig['hyva_license_key'],
            $themeConfig['hyva_project_name'],
            $output
        );

        if (!$success) {
            return false;
        }

        // Optionally set as active theme
        $this->hyvaInstaller->setAsActiveTheme($baseDir, $output);

        $output->writeln('<info>✓ Hyva theme installed successfully!</info>');

        return true;
    }
}
