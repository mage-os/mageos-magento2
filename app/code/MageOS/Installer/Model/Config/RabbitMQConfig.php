<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Config;

use MageOS\Installer\Model\Detector\RabbitMQDetector;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

/**
 * Collects RabbitMQ configuration interactively
 */
class RabbitMQConfig
{
    public function __construct(
        private readonly RabbitMQDetector $rabbitMQDetector
    ) {
    }

    /**
     * Collect RabbitMQ configuration
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param QuestionHelper $questionHelper
     * @return array{enabled: bool, host: string, port: int, user: string, password: string, virtualhost: string}|null
     */
    public function collect(
        InputInterface $input,
        OutputInterface $output,
        QuestionHelper $questionHelper
    ): ?array {
        $output->writeln('');
        $output->writeln('<info>=== RabbitMQ Configuration ===</info>');

        // Detect RabbitMQ
        $output->write('<comment>🔄 Detecting RabbitMQ...</comment>');
        $detected = $this->rabbitMQDetector->detect();

        if (!$detected) {
            $output->writeln(' <comment>⚠️</comment>');
            $output->writeln('<comment>⚠️  RabbitMQ not detected on localhost:5672</comment>');
        } else {
            $output->writeln(' <info>✓</info>');
            $output->writeln(sprintf(
                '<info>✓ Found RabbitMQ on %s:%d</info>',
                $detected['host'],
                $detected['port']
            ));
        }

        // Ask if user wants to configure RabbitMQ
        $output->writeln('');
        $enableQuestion = new ConfirmationQuestion(
            '? Configure RabbitMQ? [<comment>y/N</comment>]: ',
            false
        );
        $enabled = $questionHelper->ask($input, $output, $enableQuestion);

        if (!$enabled) {
            $output->writeln('<comment>ℹ️  Skipping RabbitMQ configuration</comment>');
            return null;
        }

        // Collect configuration
        $defaultHost = $detected['host'] ?? 'localhost';
        $defaultPort = $detected['port'] ?? 5672;

        $hostQuestion = new Question(
            sprintf('? RabbitMQ host [<comment>%s</comment>]: ', $defaultHost),
            $defaultHost
        );
        $host = $questionHelper->ask($input, $output, $hostQuestion);

        $portQuestion = new Question(
            sprintf('? RabbitMQ port [<comment>%d</comment>]: ', $defaultPort),
            (string)$defaultPort
        );
        $port = (int)$questionHelper->ask($input, $output, $portQuestion);

        $userQuestion = new Question('? RabbitMQ username [<comment>guest</comment>]: ', 'guest');
        $user = $questionHelper->ask($input, $output, $userQuestion);

        $passwordQuestion = new Question('? RabbitMQ password [<comment>guest</comment>]: ', 'guest');
        $passwordQuestion->setHidden(true);
        $passwordQuestion->setHiddenFallback(false);
        $password = $questionHelper->ask($input, $output, $passwordQuestion);

        $vhostQuestion = new Question('? RabbitMQ virtual host [<comment>/</comment>]: ', '/');
        $virtualhost = $questionHelper->ask($input, $output, $vhostQuestion);

        return [
            'enabled' => true,
            'host' => $host ?? $defaultHost,
            'port' => $port,
            'user' => $user ?? 'guest',
            'password' => $password ?? 'guest',
            'virtualhost' => $virtualhost ?? '/'
        ];
    }
}
