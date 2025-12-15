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

class SampleDataInstallationStage extends AbstractStage
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
        return 'Sample Data Installation';
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return 'Install Magento sample data';
    }

    /**
     * @inheritDoc
     */
    public function getProgressWeight(): int
    {
        // Sample data installation is heavy
        return 5;
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
    public function shouldSkip(InstallationContext $context): bool
    {
        $sampleData = $context->getSampleData();
        return !$sampleData || !$sampleData->install;
    }

    /**
     * @inheritDoc
     */
    public function execute(InstallationContext $context, OutputInterface $output): StageResult
    {
        $output->writeln('');
        $output->writeln('<comment>🔄 Installing sample data...</comment>');

        try {
            // Deploy sample data
            $sampleDataCommand = $this->application->find('sampledata:deploy');
            $sampleDataInput = new ArrayInput(['command' => 'sampledata:deploy']);
            $sampleDataInput->setInteractive(false);
            $sampleDataCommand->run($sampleDataInput, $output);

            // Run setup:upgrade to install sample data modules
            $upgradeCommand = $this->application->find('setup:upgrade');
            $upgradeInput = new ArrayInput(['command' => 'setup:upgrade']);
            $upgradeInput->setInteractive(false);
            $upgradeCommand->run($upgradeInput, $output);

            $output->writeln('<info>✓ Sample data installed</info>');
        } catch (\Exception $e) {
            $output->writeln('<comment>⚠️  Sample data installation failed: ' . $e->getMessage() . '</comment>');
            $output->writeln('<comment>   You can install it later with: bin/magento sampledata:deploy</comment>');
        }

        return StageResult::continue();
    }
}
