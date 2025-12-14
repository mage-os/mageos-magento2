<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Stage;

use MageOS\Installer\Model\Detector\DocumentRootDetector;
use MageOS\Installer\Model\InstallationContext;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Document root information stage (informational only)
 */
class DocumentRootInfoStage extends AbstractStage
{
    public function __construct(
        private readonly DocumentRootDetector $documentRootDetector
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'Document Root';
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return 'Display document root information';
    }

    /**
     * @inheritDoc
     */
    public function getProgressWeight(): int
    {
        // Informational only, no progress
        return 0;
    }

    /**
     * @inheritDoc
     */
    public function execute(InstallationContext $context, OutputInterface $output): StageResult
    {
        $baseDir = BP;
        $detection = $this->documentRootDetector->detect($baseDir);

        $output->writeln('');
        $output->writeln('<info>=== Document Root ===</info>');

        if ($detection['isPub']) {
            $output->writeln('<info>ℹ️  Detected: Document root is /pub</info>');
            $output->writeln('<info>✓ Using secure document root configuration</info>');
        } else {
            $output->writeln('<comment>ℹ️  Detected: Document root is project root</comment>');
            $output->writeln('<comment>' . $detection['recommendation'] . '</comment>');
        }

        $output->writeln('');

        // No user input needed, just continue
        return StageResult::continue();
    }
}
