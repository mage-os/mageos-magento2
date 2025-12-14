<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Theme;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

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
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param QuestionHelper $questionHelper
     * @return bool
     */
    public function install(
        string $baseDir,
        array $themeConfig,
        InputInterface $input,
        OutputInterface $output,
        QuestionHelper $questionHelper
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
            return $this->installHyva($baseDir, $themeConfig, $input, $output, $questionHelper);
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
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param QuestionHelper $questionHelper
     * @return bool
     */
    private function installHyva(
        string $baseDir,
        array $themeConfig,
        InputInterface $input,
        OutputInterface $output,
        QuestionHelper $questionHelper
    ): bool {
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
            $output->writeln('');
            $skipQuestion = new ConfirmationQuestion(
                "<question>? Hyva installation failed. Continue without Hyva theme?</question> [<comment>Y/n</comment>]: ",
                true
            );
            $skip = $questionHelper->ask($input, $output, $skipQuestion);

            if (!$skip) {
                throw new \RuntimeException('Hyva installation failed. Installation aborted.');
            }

            $output->writeln('<comment>⚠️  Continuing without Hyva theme</comment>');
            return false;
        }

        // Optionally set as active theme
        $this->hyvaInstaller->setAsActiveTheme($baseDir, $output);

        $output->writeln('<info>✓ Hyva theme installed successfully!</info>');

        return true;
    }
}
