<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Stage;

use MageOS\Installer\Model\Config\BackendConfig;
use MageOS\Installer\Model\InstallationContext;
use MageOS\Installer\Model\VO\BackendConfiguration;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Backend configuration stage
 */
class BackendConfigStage extends AbstractStage
{
    public function __construct(
        private readonly BackendConfig $backendConfig
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'Backend Configuration';
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return 'Configure admin panel path';
    }

    /**
     * @inheritDoc
     */
    public function execute(InstallationContext $context, OutputInterface $output): StageResult
    {
        // Check if already configured
        if ($context->getBackend() !== null) {
            $backend = $context->getBackend();
            \Laravel\Prompts\info(sprintf('Backend path: /%s', $backend->frontname));

            $useExisting = \Laravel\Prompts\confirm(
                label: 'Use saved backend configuration?',
                default: true
            );

            if ($useExisting) {
                return StageResult::continue();
            }
        }

        // Collect backend configuration
        $backendArray = $this->backendConfig->collect();

        // Store in context
        $context->setBackend(BackendConfiguration::fromArray($backendArray));

        return StageResult::continue();
    }
}
