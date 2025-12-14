<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Theme;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Handles Hyva theme installation
 */
class HyvaInstaller
{
    public function __construct(
        private readonly ComposerAuthManager $authManager
    ) {
    }

    /**
     * Install Hyva theme
     *
     * @param string $baseDir
     * @param string $licenseKey
     * @param string $projectName
     * @param OutputInterface $output
     * @return bool
     */
    public function install(
        string $baseDir,
        string $licenseKey,
        string $projectName,
        OutputInterface $output
    ): bool {
        try {
            // Step 1: Add Hyva credentials to auth.json
            $output->writeln('<comment>  → Adding Hyva credentials to auth.json...</comment>');
            $this->authManager->addHyvaAuth($baseDir, $licenseKey, $projectName);
            $output->writeln('<info>  ✓ Credentials added</info>');

            // Step 2: Add Hyva repository to composer.json
            $output->writeln('<comment>  → Adding Hyva repository to composer.json...</comment>');
            $this->authManager->addHyvaRepository($baseDir, $projectName);
            $output->writeln('<info>  ✓ Repository configured</info>');

            // Step 3: Run composer require
            $output->writeln('<comment>  → Installing Hyva theme via Composer (this may take a few minutes)...</comment>');
            $composerCommand = sprintf(
                'cd %s && composer require hyva-themes/magento2-default-theme --no-interaction 2>&1',
                escapeshellarg($baseDir)
            );

            $composerOutput = [];
            $returnCode = 0;
            exec($composerCommand, $composerOutput, $returnCode);

            if ($returnCode !== 0) {
                $output->writeln('<error>❌ Composer installation failed</error>');
                $output->writeln('<comment>Output:</comment>');
                foreach ($composerOutput as $line) {
                    $output->writeln('  ' . $line);
                }
                return false;
            }

            $output->writeln('<info>  ✓ Hyva theme installed via Composer</info>');

            // Step 4: Run setup:upgrade
            $output->writeln('<comment>  → Running setup:upgrade...</comment>');
            $upgradeCommand = sprintf('cd %s && bin/magento setup:upgrade 2>&1', escapeshellarg($baseDir));
            exec($upgradeCommand, $upgradeOutput, $upgradeReturnCode);

            if ($upgradeReturnCode !== 0) {
                $output->writeln('<comment>⚠️  setup:upgrade had issues (this may be normal)</comment>');
            } else {
                $output->writeln('<info>  ✓ setup:upgrade completed</info>');
            }

            // Step 5: Run di:compile
            $output->writeln('<comment>  → Running di:compile...</comment>');
            $compileCommand = sprintf('cd %s && bin/magento setup:di:compile 2>&1', escapeshellarg($baseDir));
            exec($compileCommand, $compileOutput, $compileReturnCode);

            if ($compileReturnCode === 0) {
                $output->writeln('<info>  ✓ di:compile completed</info>');
            } else {
                $output->writeln('<comment>⚠️  di:compile had issues (you may need to run it manually)</comment>');
            }

            return true;
        } catch (\Exception $e) {
            $output->writeln('<error>❌ Hyva installation failed: ' . $e->getMessage() . '</error>');
            return false;
        }
    }

    /**
     * Set Hyva as active theme
     *
     * @param string $baseDir
     * @param OutputInterface $output
     * @return bool
     */
    public function setAsActiveTheme(string $baseDir, OutputInterface $output): bool
    {
        try {
            $output->writeln('<comment>  → Setting Hyva as active theme...</comment>');

            // Set theme via CLI
            $themeCommand = sprintf(
                'cd %s && bin/magento config:set design/theme/theme_id 4 2>&1',
                escapeshellarg($baseDir)
            );
            exec($themeCommand, $themeOutput, $themeReturnCode);

            if ($themeReturnCode === 0) {
                $output->writeln('<info>  ✓ Hyva theme activated</info>');
                return true;
            } else {
                $output->writeln('<comment>⚠️  Theme activation via CLI failed</comment>');
                $output->writeln('<comment>   You can set it manually in Admin > Content > Design > Configuration</comment>');
                return false;
            }
        } catch (\Exception $e) {
            $output->writeln('<comment>⚠️  Theme activation failed: ' . $e->getMessage() . '</comment>');
            return false;
        }
    }
}
