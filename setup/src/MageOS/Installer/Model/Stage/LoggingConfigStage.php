<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Stage;

use MageOS\Installer\Model\Config\LoggingConfig;
use MageOS\Installer\Model\InstallationContext;
use MageOS\Installer\Model\VO\LoggingConfiguration;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Logging configuration stage
 */
class LoggingConfigStage extends AbstractStage
{
    public function __construct(
        private readonly LoggingConfig $loggingConfig
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'Logging Configuration';
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return 'Configure debug mode and logging';
    }

    /**
     * @inheritDoc
     */
    public function execute(InstallationContext $context, OutputInterface $output): StageResult
    {
        // Check if already configured
        if ($context->getLogging() !== null) {
            $logging = $context->getLogging();
            \Laravel\Prompts\info(sprintf(
                'Debug: %s, Log level: %s',
                $logging->debugMode ? 'ON' : 'OFF',
                $logging->logLevel
            ));

            $useExisting = \Laravel\Prompts\confirm(
                label: 'Use saved logging configuration?',
                default: true
            );

            if ($useExisting) {
                return StageResult::continue();
            }
        }

        // Collect logging configuration
        $loggingArray = $this->loggingConfig->collect();

        // Store in context
        $context->setLogging(LoggingConfiguration::fromArray($loggingArray));

        return StageResult::continue();
    }
}
