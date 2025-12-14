<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Stage;

use MageOS\Installer\Model\InstallationContext;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Completion stage - displays success message
 */
class CompletionStage extends AbstractStage
{
    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'Installation Complete';
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return 'Display installation completion message';
    }

    /**
     * @inheritDoc
     */
    public function getProgressWeight(): int
    {
        return 0;
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
        $store = $context->getStore();
        $backend = $context->getBackend();
        $admin = $context->getAdmin();
        $logging = $context->getLogging();
        $sampleData = $context->getSampleData();
        $theme = $context->getTheme();

        $adminUrl = $store && $backend
            ? rtrim($store->baseUrl, '/') . '/' . $backend->frontname
            : '';

        $output->writeln('');
        $output->writeln('<fg=green>🎉 Mage-OS Installation Complete!</>');
        $output->writeln('');
        $output->writeln('<info>=== Access Information ===</info>');
        $output->writeln('');

        if ($store) {
            $output->writeln(sprintf('  <info>🌐 Storefront:</info> %s', $store->baseUrl));
        }

        if ($adminUrl) {
            $output->writeln(sprintf('  <info>🔐 Admin Panel:</info> %s', $adminUrl));
        }

        if ($admin) {
            $output->writeln(sprintf('  <info>👤 Admin Username:</info> %s', $admin->username));
            $output->writeln(sprintf('  <info>📧 Admin Email:</info> %s', $admin->email));
        }

        $output->writeln('');
        $output->writeln('<info>=== Next Steps ===</info>');
        $output->writeln('');
        $output->writeln('  1. Clear cache:');
        $output->writeln('     <comment>bin/magento cache:clean</comment>');
        $output->writeln('');

        if ($logging && $logging->debugMode) {
            $output->writeln('  2. For production, disable debug mode:');
            $output->writeln('     <comment>bin/magento deploy:mode:set production</comment>');
            $output->writeln('');
            $output->writeln('  3. Open your store:');
        } else {
            $output->writeln('  2. Open your store:');
        }

        if ($store) {
            $output->writeln('     <comment>' . $store->baseUrl . '</comment>');
        }

        $output->writeln('');

        if ($sampleData && $sampleData->install) {
            $output->writeln('  <comment>ℹ️  Sample data has been installed for development/testing purposes</comment>');
            $output->writeln('');
        }

        if ($theme && $theme->install && $theme->theme) {
            $themeName = match($theme->theme) {
                'hyva' => 'Hyva',
                'luma' => 'Luma',
                'blank' => 'Blank',
                default => ucfirst($theme->theme)
            };
            $output->writeln(sprintf('  <comment>ℹ️  %s theme has been installed</comment>', $themeName));
            $output->writeln('');
        }

        $output->writeln('<fg=cyan>Happy coding! 🚀</>');
        $output->writeln('');

        return StageResult::continue();
    }
}
