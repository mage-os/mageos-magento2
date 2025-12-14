<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Stage;

use MageOS\Installer\Model\Config\SearchEngineConfig;
use MageOS\Installer\Model\InstallationContext;
use MageOS\Installer\Model\VO\SearchEngineConfiguration;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Search engine configuration stage
 */
class SearchEngineConfigStage extends AbstractStage
{
    public function __construct(
        private readonly SearchEngineConfig $searchEngineConfig
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'Search Engine';
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return 'Configure Elasticsearch/OpenSearch';
    }

    /**
     * @inheritDoc
     */
    public function execute(InstallationContext $context, OutputInterface $output): StageResult
    {
        // Check if already configured
        if ($context->getSearchEngine() !== null) {
            $search = $context->getSearchEngine();
            \Laravel\Prompts\info(sprintf('Search: %s (%s:%d)', $search->engine, $search->host, $search->port));

            $useExisting = \Laravel\Prompts\confirm(
                label: 'Use saved search engine configuration?',
                default: true
            );

            if ($useExisting) {
                return StageResult::continue();
            }
        }

        // Collect search engine configuration
        $searchArray = $this->searchEngineConfig->collect();

        // Store in context
        $context->setSearchEngine(SearchEngineConfiguration::fromArray($searchArray));

        return StageResult::continue();
    }
}
