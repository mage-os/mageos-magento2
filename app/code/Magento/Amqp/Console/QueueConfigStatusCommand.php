<?php
/**
 * Copyright © Mage-OS, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Amqp\Console;

use Magento\Amqp\Model\QueueConfig\ChangeDetector;
use Magento\Framework\Console\Cli;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command for checking if AMQP queue configuration is up to date
 */
class QueueConfigStatusCommand extends Command
{
    /**
     * Code for error when queue update is required.
     */
    public const EXIT_CODE_QUEUE_UPDATE_REQUIRED = 2;

    /**
     * @param ChangeDetector $changeDetector
     */
    public function __construct(
        private readonly ChangeDetector $changeDetector
    ) {
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('queue:config:amqp:status')
            ->setDescription('Checks if AMQP queue configuration requires update');
        parent::configure();
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            if ($this->changeDetector->hasChanges()) {
                $missingQueues = $this->changeDetector->getMissingQueues();
                $output->writeln(
                    '<info>AMQP queues missing: ' . implode(', ', $missingQueues) . '. ' .
                    'Run setup:upgrade to synchronize.</info>'
                );
                return self::EXIT_CODE_QUEUE_UPDATE_REQUIRED;
            }

            $output->writeln('<info>AMQP queue config is up to date.</info>');
            return Cli::RETURN_SUCCESS;

        } catch (\LogicException $e) {
            $output->writeln('<info>AMQP not configured. Skipping queue checks.</info>');
            return Cli::RETURN_SUCCESS;

        } catch (\Exception $e) {
            $output->writeln(
                '<error>Cannot connect to AMQP broker: ' . $e->getMessage() . '. ' .
                'Verify AMQP configuration.</error>'
            );
            return Cli::RETURN_FAILURE;
        }
    }
}
