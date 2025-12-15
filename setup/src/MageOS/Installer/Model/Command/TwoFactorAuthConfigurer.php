<?php

declare(strict_types=1);

namespace MageOS\Installer\Model\Command;

use MageOS\Installer\Model\VO\EnvironmentConfiguration;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Configures Two-Factor Authentication based on environment
 */
class TwoFactorAuthConfigurer
{
    public function __construct(
        private readonly ProcessRunner $processRunner
    ) {
    }

    /**
     * Configure 2FA based on environment type
     *
     * @param EnvironmentConfiguration $environment
     * @param string $baseDir
     * @param OutputInterface $output
     * @return bool True if successful
     */
    public function configure(EnvironmentConfiguration $environment, string $baseDir, OutputInterface $output): bool
    {
        $output->writeln('');
        $output->write('<comment>🔐 Configuring Two-Factor Authentication...</comment>');

        // For development environments, offer to disable 2FA
        if ($environment->isDevelopment()) {
            $output->writeln('');
            $output->writeln('<comment>   Development mode detected</comment>');

            $disable = \Laravel\Prompts\confirm(
                label: 'Disable Two-Factor Authentication for easier development?',
                default: true,
                hint: 'You can re-enable later with: bin/magento module:enable Magento_TwoFactorAuth'
            );

            if ($disable) {
                $result = $this->processRunner->runMagentoCommand(
                    'module:disable Magento_AdminAdobeImsTwoFactorAuth Magento_TwoFactorAuth',
                    $baseDir,
                    timeout: 60
                );

                if ($result->isSuccess()) {
                    $output->writeln('<info>✓ 2FA disabled for development</info>');

                    // Run setup:upgrade to apply module changes
                    $this->processRunner->runMagentoCommand('setup:upgrade', $baseDir, timeout: 120);
                    $this->processRunner->runMagentoCommand('cache:flush', $baseDir, timeout: 30);

                    return true;
                }

                $output->writeln('<comment>⚠️  Could not disable 2FA automatically</comment>');
                $output->writeln('<comment>   Disable manually: bin/magento module:disable Magento_TwoFactorAuth</comment>');
                return false;
            }

            $output->writeln('<info>✓ 2FA remains enabled</info>');
            $output->writeln('<comment>   Configure it on first admin login</comment>');
            return true;
        }

        // For production, keep 2FA enabled
        $output->writeln(' <info>✓</info>');
        $output->writeln('<info>✓ 2FA enabled (recommended for production)</info>');
        $output->writeln('<comment>   Configure your authentication app on first admin login</comment>');

        return true;
    }
}
