<?php

/**
 * Copyright © Mage-OS, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Theme\Console\Command;

use Magento\Framework\Console\Cli;
use Magento\Theme\Model\Theme\RegistrationDetector;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command for uninstalling theme and backup-code feature
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 */
class ThemeRegistrationStatusCommand extends Command
{

    public const EXIT_CODE_THEME_UPDATE_REQUIRED = 2;

    public function __construct(
        private RegistrationDetector $registrationDetector
    ) {
        $this->registrationDetector = $registrationDetector;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('theme:registration:status');
        $this->setDescription('Displays the registration status of themes.');
        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($this->registrationDetector->hasUnregisteredTheme()) {
            $missing = $this->registrationDetector->getMissingThemes();
            $output->writeln('<info>Unregistered themes detected:</info>');
            foreach ($missing as $theme) {
                $output->writeln("  - $theme");
            }
            $output->writeln('<comment>Run setup:upgrade to register themes.</comment>');
            return self::EXIT_CODE_THEME_UPDATE_REQUIRED;
        }

        $output->writeln('<info>All themes are registered.</info>');
        return Cli::RETURN_SUCCESS;
    }
}
