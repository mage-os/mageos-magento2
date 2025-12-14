<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Stage;

use MageOS\Installer\Model\Config\DatabaseConfig;
use MageOS\Installer\Model\InstallationContext;
use MageOS\Installer\Model\VO\DatabaseConfiguration;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Database configuration stage
 */
class DatabaseConfigStage extends AbstractStage
{
    public function __construct(
        private readonly DatabaseConfig $databaseConfig
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'Database Configuration';
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return 'Configure database connection settings';
    }

    /**
     * @inheritDoc
     */
    public function execute(InstallationContext $context, OutputInterface $output): StageResult
    {
        // If resuming and database config exists, ask if user wants to use it
        if ($context->getDatabase() !== null) {
            $db = $context->getDatabase();

            \Laravel\Prompts\info(sprintf('Found saved database config: %s@%s/%s', $db->user, $db->host, $db->name));

            $useExisting = \Laravel\Prompts\confirm(
                label: 'Use saved database configuration?',
                default: true,
                hint: 'Select No to reconfigure'
            );

            if ($useExisting) {
                // But we need to re-prompt for password (it wasn't saved)
                if (empty($db->password)) {
                    $password = \Laravel\Prompts\password(
                        label: 'Database password',
                        hint: 'Password was not saved for security'
                    );

                    // Create new config with password
                    $context->setDatabase(new DatabaseConfiguration(
                        $db->host,
                        $db->name,
                        $db->user,
                        $password,
                        $db->prefix
                    ));
                }

                return StageResult::continue();
            }
        }

        // Collect database configuration using existing collector
        $dbArray = $this->databaseConfig->collect();

        // Convert to VO and store in context
        $context->setDatabase(DatabaseConfiguration::fromArray($dbArray));

        return StageResult::continue();
    }
}
