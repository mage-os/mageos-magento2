<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Stage;

use MageOS\Installer\Model\InstallationContext;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Summary stage - displays configuration summary and confirms installation
 */
class SummaryStage extends AbstractStage
{
    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'Configuration Summary';
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return 'Review configuration and confirm installation';
    }

    /**
     * @inheritDoc
     */
    public function getProgressWeight(): int
    {
        // Summary doesn't take time
        return 0;
    }

    /**
     * @inheritDoc
     */
    public function execute(InstallationContext $context, OutputInterface $output): StageResult
    {
        $output->writeln('');
        $output->writeln('<fg=cyan>🎯 Configuration complete! Here\'s what will be installed:</>');
        $output->writeln('');

        // Display all configuration
        if ($env = $context->getEnvironment()) {
            $output->writeln(sprintf(
                '  <info>Environment:</info> %s (mode: %s)',
                ucfirst($env->type),
                $env->mageMode
            ));
        }

        if ($db = $context->getDatabase()) {
            $output->writeln(sprintf(
                '  <info>Database:</info> %s@%s/%s',
                $db->user,
                $db->host,
                $db->name
            ));
        }

        if ($admin = $context->getAdmin()) {
            $output->writeln(sprintf('  <info>Admin:</info> %s', $admin->email));
        }

        if ($store = $context->getStore()) {
            $output->writeln(sprintf('  <info>Store:</info> %s', $store->baseUrl));
        }

        if ($backend = $context->getBackend()) {
            $output->writeln(sprintf('  <info>Backend Path:</info> /%s', $backend->frontname));
        }

        if ($search = $context->getSearchEngine()) {
            $output->writeln(sprintf(
                '  <info>Search Engine:</info> %s (%s:%d)',
                $search->engine,
                $search->host,
                $search->port
            ));
        }

        if ($redis = $context->getRedis()) {
            if ($redis->isEnabled()) {
                $features = [];
                if ($redis->session) {
                    $features[] = 'Sessions';
                }
                if ($redis->cache) {
                    $features[] = 'Cache';
                }
                if ($redis->fpc) {
                    $features[] = 'FPC';
                }
                $output->writeln(sprintf('  <info>Redis:</info> %s', implode(', ', $features)));
            }
        }

        if ($rabbitmq = $context->getRabbitMQ()) {
            if ($rabbitmq->enabled) {
                $output->writeln(sprintf(
                    '  <info>RabbitMQ:</info> %s:%d',
                    $rabbitmq->host,
                    $rabbitmq->port
                ));
            }
        }

        if ($logging = $context->getLogging()) {
            $output->writeln(sprintf('  <info>Debug Mode:</info> %s', $logging->debugMode ? 'ON' : 'OFF'));
            $output->writeln(sprintf('  <info>Log Level:</info> %s', $logging->logLevel));
        }

        if ($sampleData = $context->getSampleData()) {
            if ($sampleData->install) {
                $output->writeln('  <info>Sample Data:</info> Yes');
            }
        }

        if ($theme = $context->getTheme()) {
            if ($theme->install && $theme->theme) {
                $themeName = match ($theme->theme) {
                    'hyva' => 'Hyva',
                    'luma' => 'Luma',
                    'blank' => 'Blank',
                    default => ucfirst($theme->theme)
                };
                $output->writeln(sprintf('  <info>Theme:</info> %s', $themeName));
            }
        }

        $output->writeln('');

        // Confirm installation
        $confirm = \Laravel\Prompts\confirm(
            label: 'Proceed with installation?',
            default: true
        );

        if (!$confirm) {
            // Ask if they want to go back or abort
            $goBack = \Laravel\Prompts\confirm(
                label: 'Go back to change configuration?',
                default: true,
                hint: 'Select No to cancel installation completely'
            );

            return $goBack ? StageResult::back() : StageResult::abort('Installation cancelled');
        }

        return StageResult::continue();
    }
}
