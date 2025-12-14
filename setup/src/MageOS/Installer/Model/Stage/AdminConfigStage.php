<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Stage;

use MageOS\Installer\Model\Config\AdminConfig;
use MageOS\Installer\Model\InstallationContext;
use MageOS\Installer\Model\VO\AdminConfiguration;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Admin account configuration stage
 */
class AdminConfigStage extends AbstractStage
{
    public function __construct(
        private readonly AdminConfig $adminConfig
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'Admin Account';
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return 'Configure admin user account';
    }

    /**
     * @inheritDoc
     */
    public function execute(InstallationContext $context, OutputInterface $output): StageResult
    {
        // Check if already configured from resume
        if ($context->getAdmin() !== null) {
            $admin = $context->getAdmin();

            // Show what we have
            \Laravel\Prompts\info(sprintf('Admin: %s (%s)', $admin->username, $admin->email));

            $useExisting = \Laravel\Prompts\confirm(
                label: 'Use saved admin configuration?',
                default: true,
                hint: 'Password will be re-prompted for security'
            );

            if ($useExisting) {
                // Always re-prompt for password (never saved)
                if (empty($admin->password)) {
                    $password = \Laravel\Prompts\password(
                        label: 'Admin password',
                        placeholder: '••••••••',
                        hint: 'Must be 7+ characters with both letters and numbers',
                        validate: function (string $value) {
                            if (empty($value)) {
                                return 'Password cannot be empty';
                            }
                            if (strlen($value) < 7) {
                                return 'Password must be at least 7 characters long';
                            }

                            $hasAlpha = preg_match('/[a-zA-Z]/', $value);
                            $hasNumeric = preg_match('/[0-9]/', $value);

                            if (!$hasAlpha || !$hasNumeric) {
                                return 'Password must include both alphabetic and numeric characters';
                            }

                            return null;
                        }
                    );

                    // Update context with password
                    $context->setAdmin(new AdminConfiguration(
                        $admin->firstName,
                        $admin->lastName,
                        $admin->email,
                        $admin->username,
                        $password
                    ));
                }

                return StageResult::continue();
            }
        }

        // Collect admin configuration
        $adminArray = $this->adminConfig->collect();

        // Store in context
        $context->setAdmin(AdminConfiguration::fromArray($adminArray));

        return StageResult::continue();
    }
}
