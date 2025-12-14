<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Stage;

use MageOS\Installer\Model\InstallationContext;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Welcome stage - displays welcome message
 */
class WelcomeStage extends AbstractStage
{
    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'Welcome';
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return 'Display welcome message and installation overview';
    }

    /**
     * @inheritDoc
     */
    public function canGoBack(): bool
    {
        // Can't go back from welcome screen
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getProgressWeight(): int
    {
        // Welcome screen is basically 0% progress
        return 0;
    }

    /**
     * @inheritDoc
     */
    public function execute(InstallationContext $context, OutputInterface $output): StageResult
    {
        $output->writeln('');
        $output->writeln('<fg=cyan>🚀 Welcome to Mage-OS Interactive Installer!</>');
        $output->writeln('');
        $output->writeln('This wizard will guide you through the installation process:');
        $output->writeln('');
        $output->writeln('  • Database configuration');
        $output->writeln('  • Admin account setup');
        $output->writeln('  • Store configuration');
        $output->writeln('  • Optional services (Redis, RabbitMQ, etc.)');
        $output->writeln('  • Theme installation');
        $output->writeln('');
        $output->writeln('<fg=yellow>💡 You can go back at any time to change your answers.</>');
        $output->writeln('<fg=yellow>💡 Your configuration will be saved if installation fails.</>');
        $output->writeln('');

        // Simple confirmation to start
        $start = \Laravel\Prompts\confirm(
            label: 'Ready to begin?',
            default: true
        );

        if (!$start) {
            return StageResult::abort('Installation cancelled by user');
        }

        return StageResult::continue();
    }
}
