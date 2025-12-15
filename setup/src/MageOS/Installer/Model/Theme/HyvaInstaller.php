<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Theme;

use MageOS\Installer\Model\Command\ProcessRunner;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Handles Hyva theme installation
 */
class HyvaInstaller
{
    public function __construct(
        private readonly ComposerAuthManager $authManager,
        private readonly ProcessRunner $processRunner
    ) {
    }

    /**
     * Install Hyva theme via Composer (before Magento installation)
     *
     * @param string $baseDir
     * @param string $projectKey
     * @param string $apiToken
     * @param OutputInterface $output
     * @return bool
     */
    public function install(
        string $baseDir,
        string $projectKey,
        string $apiToken,
        OutputInterface $output
    ): bool {
        try {
            // Step 1: Add Hyva credentials to auth.json
            $output->writeln('<comment>  → Adding Hyva credentials to auth.json...</comment>');
            $this->authManager->addHyvaAuth($baseDir, $projectKey, $apiToken);
            $output->writeln('<info>  ✓ Credentials added</info>');

            // Step 2: Add Hyva repository to composer.json
            $output->writeln('<comment>  → Adding Hyva repository to composer.json...</comment>');
            $this->authManager->addHyvaRepository($baseDir, $projectKey);
            $output->writeln('<info>  ✓ Repository configured</info>');

            // Step 3: Run composer require
            $output->writeln('<comment>  → Installing Hyva theme via Composer (this may take a few minutes)...</comment>');

            $result = $this->processRunner->run(
                ['composer', 'require', 'hyva-themes/magento2-default-theme', '--no-interaction'],
                $baseDir,
                timeout: 600 // Composer can take time
            );

            if (!$result->isSuccess()) {
                $output->writeln('<error>❌ Composer installation failed</error>');
                $output->writeln('');

                // Check for common authentication errors
                $outputText = $result->getCombinedOutput();
                if (str_contains($outputText, '401') || str_contains($outputText, 'Unauthorized') || str_contains($outputText, 'authentication')) {
                    $output->writeln('<error>Authentication Error:</error>');
                    $output->writeln('<comment>  Your Hyva license key or project name appears to be incorrect.</comment>');
                    $output->writeln('<comment>  Please verify your credentials at: https://www.hyva.io/hyva-theme-license.html</comment>');
                } elseif (str_contains($outputText, '404') || str_contains($outputText, 'Not Found')) {
                    $output->writeln('<error>Not Found Error:</error>');
                    $output->writeln('<comment>  The Hyva package was not found. Please check your project name.</comment>');
                } else {
                    $output->writeln('<comment>Composer output:</comment>');
                    foreach ($composerOutput as $line) {
                        $output->writeln('  ' . $line);
                    }
                }

                $output->writeln('');
                $output->writeln('<comment>ℹ️  You can install Hyva manually later with:</comment>');
                $output->writeln('<comment>   composer require hyva-themes/magento2-default-theme</comment>');

                return false;
            }

            $output->writeln('<info>  ✓ Hyva theme installed via Composer</info>');
            $output->writeln('<comment>     Theme will be available after Magento installation completes</comment>');

            return true;
        } catch (\Exception $e) {
            $output->writeln('<error>❌ Hyva installation failed: ' . $e->getMessage() . '</error>');
            return false;
        }
    }
}
