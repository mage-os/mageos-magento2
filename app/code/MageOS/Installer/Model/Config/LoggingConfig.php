<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Config;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * Collects debug and logging configuration interactively
 */
class LoggingConfig
{
    /**
     * Collect logging configuration
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param QuestionHelper $questionHelper
     * @return array{debugMode: bool, logHandler: string, logLevel: string}
     */
    public function collect(
        InputInterface $input,
        OutputInterface $output,
        QuestionHelper $questionHelper
    ): array {
        $output->writeln('');
        $output->writeln('<info>=== Debug & Logging ===</info>');

        // Debug mode
        $debugQuestion = new ConfirmationQuestion(
            '? Enable debug mode? [<comment>Y/n</comment>]: ',
            true
        );
        $debugMode = $questionHelper->ask($input, $output, $debugQuestion);

        // Log handler
        $handlerQuestion = new ChoiceQuestion(
            '? Log handler [<comment>file</comment>]: ',
            ['file', 'syslog', 'database'],
            'file'
        );
        $logHandler = $questionHelper->ask($input, $output, $handlerQuestion);

        // Log level (based on debug mode)
        $defaultLevel = $debugMode ? 'debug' : 'error';
        $levelQuestion = new ChoiceQuestion(
            sprintf('? Log level [<comment>%s</comment>]: ', $defaultLevel),
            ['debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency'],
            $defaultLevel
        );
        $logLevel = $questionHelper->ask($input, $output, $levelQuestion);

        return [
            'debugMode' => (bool)$debugMode,
            'logHandler' => $logHandler ?? 'file',
            'logLevel' => $logLevel ?? $defaultLevel
        ];
    }
}
