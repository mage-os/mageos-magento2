<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Theme;

use Symfony\Component\Console\Output\OutputInterface;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\warning;

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
     *     hyva_project_key: string|null,
     *     hyva_api_token: string|null
     * } $themeConfig
     * @param OutputInterface $output
     * @return bool
     */
    public function install(
        string $baseDir,
        array $themeConfig,
        OutputInterface $output
    ): bool {
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
     *     hyva_project_key: string|null,
     *     hyva_api_token: string|null
     * } $themeConfig
     * @param OutputInterface $output
     * @return bool
     */
    private function installHyva(
        string $baseDir,
        array $themeConfig,
        OutputInterface $output
    ): bool {
        if (empty($themeConfig['hyva_project_key']) || empty($themeConfig['hyva_api_token'])) {
            warning('Hyva credentials are required');
            return false;
        }

        $success = $this->hyvaInstaller->install(
            $baseDir,
            $themeConfig['hyva_project_key'],
            $themeConfig['hyva_api_token'],
            $output
        );

        if (!$success) {
            $skip = confirm(
                label: 'Hyva installation failed. Continue without Hyva theme?',
                default: true
            );

            if (!$skip) {
                throw new \RuntimeException('Hyva installation failed. Installation aborted.');
            }

            warning('Continuing without Hyva theme (Luma will be used)');
            return false;
        }

        $output->writeln('<info>✓ Hyva theme ready! Will be activated during Magento installation</info>');

        return true;
    }
}
