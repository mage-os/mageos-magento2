<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Stage;

use MageOS\Installer\Model\InstallationContext;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Magento installation stage
 *
 * This is the "point of no return" - once Magento installation starts,
 * we can't go back (database will be modified).
 */
class MagentoInstallationStage extends AbstractStage
{
    public function __construct(
        private readonly Application $application
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'Magento Installation';
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return 'Run Magento setup:install and configure services';
    }

    /**
     * @inheritDoc
     */
    public function canGoBack(): bool
    {
        // Can't go back once installation starts!
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getProgressWeight(): int
    {
        // Installation is the heaviest operation
        return 10;
    }

    /**
     * @inheritDoc
     */
    public function execute(InstallationContext $context, OutputInterface $output): StageResult
    {
        $output->writeln('');
        $output->writeln('<fg=cyan>🚀 Starting Magento installation...</>');
        $output->writeln('');
        $output->writeln('<fg=yellow>⚠️  This will modify your database. You cannot go back after this point.</>');
        $output->writeln('');

        // Final confirmation
        $confirm = \Laravel\Prompts\confirm(
            label: 'Proceed with installation?',
            default: true
        );

        if (!$confirm) {
            return StageResult::abort('Installation cancelled by user');
        }

        // Build setup:install arguments
        $arguments = $this->buildSetupInstallArguments($context);

        // Get setup:install command
        $setupInstallCommand = $this->application->find('setup:install');

        // Create input
        $setupInput = new ArrayInput($arguments);
        $setupInput->setInteractive(false);

        // Run setup:install
        $output->writeln('<comment>🔄 Installing Magento core...</comment>');
        $returnCode = $setupInstallCommand->run($setupInput, $output);

        if ($returnCode !== 0) {
            return StageResult::abort('Magento installation failed. Check errors above.');
        }

        $output->writeln('');
        $output->writeln('<info>✓ Magento core installed successfully!</info>');

        return StageResult::continue();
    }

    /**
     * Build arguments array for setup:install command
     *
     * @param InstallationContext $context
     * @return array<string, mixed>
     */
    private function buildSetupInstallArguments(InstallationContext $context): array
    {
        $db = $context->getDatabase();
        $admin = $context->getAdmin();
        $store = $context->getStore();
        $backend = $context->getBackend();
        $search = $context->getSearchEngine();

        if (!$db || !$admin || !$store || !$backend || !$search) {
            throw new \RuntimeException('Missing required configuration for installation');
        }

        $arguments = [
            'command' => 'setup:install',
            '--db-host' => $db->host,
            '--db-name' => $db->name,
            '--db-user' => $db->user,
            '--db-password' => $db->password,
            '--admin-firstname' => $admin->firstName,
            '--admin-lastname' => $admin->lastName,
            '--admin-email' => $admin->email,
            '--admin-user' => $admin->username,
            '--admin-password' => $admin->password,
            '--base-url' => $store->baseUrl,
            '--backend-frontname' => $backend->frontname,
            '--language' => $store->language,
            '--currency' => $store->currency,
            '--timezone' => $store->timezone,
            '--use-rewrites' => $store->useRewrites ? '1' : '0',
            '--search-engine' => $search->engine,
            '--cleanup-database' => true
        ];

        // Add search engine host
        $hostKey = $search->isOpenSearch() ? '--opensearch-host' : '--elasticsearch-host';
        $arguments[$hostKey] = $search->getHostWithPort();

        // Add optional parameters
        if (!empty($db->prefix)) {
            $arguments['--db-prefix'] = $db->prefix;
        }

        if (!empty($search->prefix)) {
            $prefixKey = $search->isOpenSearch() ? '--opensearch-index-prefix' : '--elasticsearch-index-prefix';
            $arguments[$prefixKey] = $search->prefix;
        }

        return $arguments;
    }
}
