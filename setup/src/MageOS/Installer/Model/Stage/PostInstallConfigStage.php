<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Stage;

use MageOS\Installer\Model\Command\CronConfigurer;
use MageOS\Installer\Model\Command\EmailConfigurer;
use MageOS\Installer\Model\Command\ModeConfigurer;
use MageOS\Installer\Model\Config\CronConfig;
use MageOS\Installer\Model\Config\EmailConfig;
use MageOS\Installer\Model\InstallationContext;
use MageOS\Installer\Model\VO\CronConfiguration;
use MageOS\Installer\Model\VO\EmailConfiguration;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Post-installation configuration stage (Cron, Email, etc.)
 */
class PostInstallConfigStage extends AbstractStage
{
    public function __construct(
        private readonly CronConfig $cronConfig,
        private readonly EmailConfig $emailConfig,
        private readonly CronConfigurer $cronConfigurer,
        private readonly EmailConfigurer $emailConfigurer,
        private readonly ModeConfigurer $modeConfigurer
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

        return StageResult::continue();
    }
}
