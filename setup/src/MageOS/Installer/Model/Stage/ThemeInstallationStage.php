<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Stage;

use MageOS\Installer\Model\InstallationContext;
use MageOS\Installer\Model\Theme\ThemeInstaller;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Theme installation stage (runs BEFORE Magento installation)
 */
class ThemeInstallationStage extends AbstractStage
{
    public function __construct(
        private readonly ThemeInstaller $themeInstaller
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'Theme Installation';
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return 'Install selected theme via Composer';
    }

    /**
     * @inheritDoc
     */
    public function getProgressWeight(): int
    {
        // Theme installation via Composer takes some time
        return 3;
    }

    /**
     * @inheritDoc
     */
    public function canGoBack(): bool
    {
        // Once we start installing packages, we can't really go back
        return false;
    }

    /**
     * @inheritDoc
     */
    public function shouldSkip(InstallationContext $context): bool
    {
        $theme = $context->getTheme();
        return !$theme || !$theme->install || empty($theme->theme);
    }

    /**
     * @inheritDoc
     */
    public function execute(InstallationContext $context, OutputInterface $output): StageResult
    {
        $theme = $context->getTheme();

        if (!$theme || !$theme->install) {
            return StageResult::continue();
        }

        $baseDir = BP;

        // Install theme
        $this->themeInstaller->install($baseDir, $theme->toArray(), $output);

        return StageResult::continue();
    }
}
