<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Stage;

use MageOS\Installer\Model\Command\CronConfigurer;
use MageOS\Installer\Model\Command\EmailConfigurer;
use MageOS\Installer\Model\Command\IndexerConfigurer;
use MageOS\Installer\Model\Command\ModeConfigurer;
use MageOS\Installer\Model\Command\ProcessRunner;
use MageOS\Installer\Model\Command\ThemeConfigurer;
use MageOS\Installer\Model\Command\TwoFactorAuthConfigurer;
use MageOS\Installer\Model\Config\CronConfig;
use MageOS\Installer\Model\Config\EmailConfig;
use MageOS\Installer\Model\InstallationContext;
use MageOS\Installer\Model\VO\CronConfiguration;
use MageOS\Installer\Model\VO\EmailConfiguration;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Post-installation configuration stage (Cron, Email, Theme, Indexers, etc.)
 */
class PostInstallConfigStage extends AbstractStage
{
    public function __construct(
        private readonly CronConfig $cronConfig,
        private readonly EmailConfig $emailConfig,
        private readonly CronConfigurer $cronConfigurer,
        private readonly EmailConfigurer $emailConfigurer,
        private readonly ModeConfigurer $modeConfigurer,
        private readonly ThemeConfigurer $themeConfigurer,
        private readonly IndexerConfigurer $indexerConfigurer,
        private readonly TwoFactorAuthConfigurer $twoFactorAuthConfigurer,
        private readonly ProcessRunner $processRunner
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'Post-Installation Configuration';
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return 'Configure cron and email';
    }

    /**
     * @inheritDoc
     */
    public function getProgressWeight(): int
    {
        return 1;
    }

    /**
     * @inheritDoc
     */
    public function canGoBack(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function execute(InstallationContext $context, OutputInterface $output): StageResult
    {
        $output->writeln('');
        $output->writeln('<fg=cyan>═══════════════════════════════════════════════════════</>');
        $output->writeln('<fg=cyan>Post-Installation Configuration</>');
        $output->writeln('<fg=cyan>═══════════════════════════════════════════════════════</>');

        // Collect cron configuration
        $cronArray = $this->cronConfig->collect();
        $cronConfig = CronConfiguration::fromArray($cronArray);
        $context->setCron($cronConfig);

        if ($cronConfig->configure) {
            $this->cronConfigurer->configure(BP, $output);
        }

        // Collect email configuration
        $emailArray = $this->emailConfig->collect();
        $emailConfig = EmailConfiguration::fromArray($emailArray);
        $context->setEmail($emailConfig);

        if ($emailConfig->configure) {
            $this->emailConfigurer->configure($emailConfig, BP, $output);
        }

        // Set Magento mode based on environment
        $env = $context->getEnvironment();
        if ($env) {
            $this->modeConfigurer->setMode($env->mageMode, BP, $output);
        }

        // Apply selected theme to store view
        $theme = $context->getTheme();
        if ($theme) {
            $this->themeConfigurer->apply($theme, BP, $output);
        }

        // Set indexers to schedule mode for better performance
        $this->indexerConfigurer->setScheduleMode(BP, $output);

        // Configure 2FA based on environment
        if ($env) {
            $this->twoFactorAuthConfigurer->configure($env, BP, $output);
        }

        // Configure admin session lifetime for development environments
        if ($env && $env->isDevelopment()) {
            $this->configureAdminSession($output);
        }

        return StageResult::continue();
    }

    /**
     * Configure admin session lifetime for development
     *
     * @param OutputInterface $output
     * @return void
     */
    private function configureAdminSession(OutputInterface $output): void
    {
        $output->writeln('');
        $output->write('<comment>⏱️  Extending admin session lifetime for development...</comment>');

        // Extend to 1 week (604800 seconds) for dev convenience
        $result = $this->processRunner->runMagentoCommand(
            'config:set admin/security/session_lifetime 604800',
            BP,
            timeout: 30
        );

        if ($result->isSuccess()) {
            $output->writeln(' <info>✓</info>');
            $output->writeln('<info>✓ Admin session extended to 7 days (dev mode)</info>');
        }
    }
}
