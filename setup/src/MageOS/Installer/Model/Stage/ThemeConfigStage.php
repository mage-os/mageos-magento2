<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Stage;

use MageOS\Installer\Model\Config\ThemeConfig;
use MageOS\Installer\Model\InstallationContext;
use MageOS\Installer\Model\VO\ThemeConfiguration;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Theme configuration stage
 */
class ThemeConfigStage extends AbstractStage
{
    public function __construct(
        private readonly ThemeConfig $themeConfig
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'Theme Selection';
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return 'Choose theme to install';
    }

    /**
     * @inheritDoc
     */
    public function execute(InstallationContext $context, OutputInterface $output): StageResult
    {
        // Check if already configured
        if ($context->getTheme() !== null) {
            $theme = $context->getTheme();

            if ($theme->install) {
                \Laravel\Prompts\info(sprintf('Theme: %s', ucfirst($theme->theme)));
            } else {
                \Laravel\Prompts\info('Theme: None');
            }

            $useExisting = \Laravel\Prompts\confirm(
                label: 'Use saved theme configuration?',
                default: true
            );

            if ($useExisting) {
                return StageResult::continue();
            }
        }

        // Collect theme configuration
        $themeArray = $this->themeConfig->collect();

        // Store in context
        $context->setTheme(ThemeConfiguration::fromArray($themeArray));

        return StageResult::continue();
    }
}
