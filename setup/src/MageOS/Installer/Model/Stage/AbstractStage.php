<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Stage;

use MageOS\Installer\Model\InstallationContext;

/**
 * Abstract base stage with common functionality
 */
abstract class AbstractStage implements InstallationStageInterface
{
    /**
     * @inheritDoc
     */
    public function canGoBack(): bool
    {
        // By default, most stages allow going back
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getProgressWeight(): int
    {
        // Default weight for config collection stages
        return 1;
    }

    /**
     * @inheritDoc
     */
    public function shouldSkip(InstallationContext $context): bool
    {
        // By default, don't skip
        return false;
    }

    /**
     * Ask if user wants to go back
     *
     * @return bool
     */
    protected function askGoBack(): bool
    {
        if (!$this->canGoBack()) {
            return false;
        }

        return \Laravel\Prompts\confirm(
            label: 'Go back to previous step?',
            default: false,
            hint: 'You can change your previous answers'
        );
    }
}
