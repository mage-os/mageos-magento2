<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Stage;

use MageOS\Installer\Model\Config\StoreConfig;
use MageOS\Installer\Model\InstallationContext;
use MageOS\Installer\Model\VO\StoreConfiguration;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Store configuration stage
 */
class StoreConfigStage extends AbstractStage
{
    public function __construct(
        private readonly StoreConfig $storeConfig
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'Store Configuration';
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return 'Configure store URL, language, timezone, and currency';
    }

    /**
     * @inheritDoc
     */
    public function execute(InstallationContext $context, OutputInterface $output): StageResult
    {
        // Check if already configured
        if ($context->getStore() !== null) {
            $store = $context->getStore();
            \Laravel\Prompts\info(sprintf('Store: %s (%s, %s)', $store->baseUrl, $store->language, $store->currency));

            $useExisting = \Laravel\Prompts\confirm(
                label: 'Use saved store configuration?',
                default: true
            );

            if ($useExisting) {
                return StageResult::continue();
            }
        }

        // Collect store configuration
        $baseDir = BP; // Magento base directory constant
        $storeArray = $this->storeConfig->collect($baseDir);

        // Store in context
        $context->setStore(StoreConfiguration::fromArray($storeArray));

        return StageResult::continue();
    }
}
