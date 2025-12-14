<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Stage;

use MageOS\Installer\Model\Checker\PermissionChecker;
use MageOS\Installer\Model\InstallationContext;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Permission check stage - verifies file permissions before installation
 */
class PermissionCheckStage extends AbstractStage
{
    public function __construct(
        private readonly PermissionChecker $permissionChecker
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'Permission Check';
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return 'Verify file permissions';
    }

    /**
     * @inheritDoc
     */
    public function getProgressWeight(): int
    {
        // Quick check
        return 0;
    }

    /**
     * @inheritDoc
     */
    public function canGoBack(): bool
    {
        // Can't really change permissions from inside the installer
        return false;
    }

    /**
     * @inheritDoc
     */
    public function execute(InstallationContext $context, OutputInterface $output): StageResult
    {
        $output->writeln('');
        $output->write('<comment>🔄 Checking file permissions...</comment>');

        $baseDir = BP;
        $result = $this->permissionChecker->check($baseDir);

        if ($result['success']) {
            $output->writeln(' <info>✓</info>');
            $output->writeln('<info>✓ All directories are writable</info>');
            return StageResult::continue();
        }

        // Permissions missing
        $output->writeln(' <error>❌</error>');
        $output->writeln('');
        $output->writeln('<error>Missing write permissions to the following paths:</error>');

        foreach ($result['missing'] as $path) {
            $output->writeln(sprintf('  <error>• %s</error>', $path));
        }

        $output->writeln('');
        $output->writeln('<comment>To fix permissions, run these commands:</comment>');
        $output->writeln('');

        foreach ($result['commands'] as $command) {
            if (empty($command)) {
                $output->writeln('');
            } else {
                $output->writeln('  <comment>' . $command . '</comment>');
            }
        }

        $output->writeln('');
        $output->writeln('<comment>Then run the installer again: bin/magento install</comment>');
        $output->writeln('');

        return StageResult::abort('Permission check failed');
    }
}
