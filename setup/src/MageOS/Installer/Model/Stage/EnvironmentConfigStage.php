<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Stage;

use MageOS\Installer\Model\Config\EnvironmentConfig;
use MageOS\Installer\Model\InstallationContext;
use MageOS\Installer\Model\VO\EnvironmentConfiguration;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Environment configuration stage
 */
class EnvironmentConfigStage extends AbstractStage
{
    public function __construct(
        private readonly EnvironmentConfig $environmentConfig
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'Environment Configuration';
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return 'Configure environment type (development/production)';
    }

    /**
     * @inheritDoc
     */
    public function execute(InstallationContext $context, OutputInterface $output): StageResult
    {
        // Check if already configured from resume
        if ($context->getEnvironment() !== null) {
            $env = $context->getEnvironment();
            \Laravel\Prompts\info(sprintf('Environment: %s (mode: %s)', ucfirst($env->type), $env->mageMode));

            $useExisting = \Laravel\Prompts\confirm(
                label: 'Use saved environment configuration?',
                default: true
            );

            if ($useExisting) {
                return StageResult::continue();
            }
        }

        // Collect environment configuration
        $envArray = $this->environmentConfig->collect();

        // Store in context
        $context->setEnvironment(EnvironmentConfiguration::fromArray($envArray));

        return StageResult::continue();
    }
}
