<?php
/**
 * Copyright © Mage-OS, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MysqlMq\Console;

use Magento\MysqlMq\Model\QueueConfig\ChangeDetector;
use Magento\Framework\Console\Cli;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command for checking if Queue propagation is up to date
 */
class QueueConfigStatusCommand extends Command
{
    /**
     * Code for error when consumer update is required.
     */
    public const EXIT_CODE_QUEUE_UPDATE_REQUIRED = 2;

    /**
     * @var ChangeDetector
     */
    private ChangeDetector $changeDetector;

    /**
     * ConfigStatusCommand constructor.
     * @param ChangeDetector $changeDetector
     */
    public function __construct(ChangeDetector $changeDetector)
    {
        $this->changeDetector = $changeDetector;
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('queue:config:status')
            ->setDescription('Checks if defined queues in configuration exist in the database.');
        parent::configure();
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->changeDetector->hasChanges()) {
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
