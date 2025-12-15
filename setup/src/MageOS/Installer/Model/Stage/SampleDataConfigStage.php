<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Stage;

use MageOS\Installer\Model\Config\SampleDataConfig;
use MageOS\Installer\Model\InstallationContext;
use MageOS\Installer\Model\VO\SampleDataConfiguration;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Sample data configuration stage
 */
class SampleDataConfigStage extends AbstractStage
{
    public function __construct(
        private readonly SampleDataConfig $sampleDataConfig
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'Sample Data';
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return 'Choose whether to install sample data';
    }

    /**
     * @inheritDoc
     */
    public function execute(InstallationContext $context, OutputInterface $output): StageResult
    {
        // Check if already configured
        if ($context->getSampleData() !== null) {
            $sampleData = $context->getSampleData();
            \Laravel\Prompts\info(sprintf('Sample data: %s', $sampleData->install ? 'Yes' : 'No'));

            $useExisting = \Laravel\Prompts\confirm(
                label: 'Use saved sample data configuration?',
                default: true
            );

            if ($useExisting) {
                return StageResult::continue();
            }
        }

        // Collect sample data configuration
        $sampleDataArray = $this->sampleDataConfig->collect();

        // Store in context
        $context->setSampleData(SampleDataConfiguration::fromArray($sampleDataArray));

        return StageResult::continue();
    }
}
