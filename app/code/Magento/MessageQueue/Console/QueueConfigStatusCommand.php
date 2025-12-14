<?php
/**
 * Copyright © Mage-OS, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MessageQueue\Console;

use Magento\Framework\Console\Cli;
use Magento\MessageQueue\Model\QueueConfig\ChangeDetectorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command for checking if Queue configuration is up to date
 */
class QueueConfigStatusCommand extends Command
{
    /**
     * Code for error when queue update is required.
     */
    public const EXIT_CODE_QUEUE_UPDATE_REQUIRED = 2;

    /**
     * Constructor
     *
     * @param ChangeDetectorInterface[] $changeDetectors
     */
    public function __construct(
        private readonly array $changeDetectors = []
    ) {
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('queue:config:status')
            ->setDescription('Checks if defined queues in configuration exist in the queue system.');
        parent::configure();
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $hasChanges = false;

        foreach ($this->changeDetectors as $changeDetector) {
            if ($changeDetector->hasChanges()) {
                $hasChanges = true;
                break;
            }
        }

        if ($hasChanges) {
            $output->writeln(
                '<info>Queue config files have changed. ' .
                'Run setup:upgrade command to synchronize queue config.</info>'
            );
            return self::EXIT_CODE_QUEUE_UPDATE_REQUIRED;
        }

        $output->writeln('<info>Queue config files are up to date.</info>');
        return Cli::RETURN_SUCCESS;
    }
}
