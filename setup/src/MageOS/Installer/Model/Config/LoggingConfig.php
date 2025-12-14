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

        // Log handler with descriptions
        $handlerChoices = [
            'file' => 'File (var/log/system.log - recommended)',
            'syslog' => 'Syslog (system logging daemon)',
            'database' => 'Database (log table in database)'
        ];

        $choices = [];
        $choiceMap = [];
        $index = 0;

        foreach ($handlerChoices as $code => $description) {
            $choices[] = $description;
            $choiceMap[$index] = $code;
            $index++;
        }

        $output->writeln('');
        $handlerQuestion = new ChoiceQuestion(
            '? Log handler: ',
            $choices,
            0  // file is default
        );
        $selected = $questionHelper->ask($input, $output, $handlerQuestion);

        // Extract code from selected choice
        $logHandler = 'file';
        foreach ($choiceMap as $idx => $code) {
            if ($choices[$idx] === $selected) {
                $logHandler = $code;
                break;
            }
        }

        // Log level (based on debug mode)
        $defaultLevel = $debugMode ? 'debug' : 'error';

        $levelChoices = [
            'debug' => 'Debug (most verbose - development)',
            'info' => 'Info (informational messages)',
            'notice' => 'Notice (normal but significant)',
            'warning' => 'Warning (potential issues)',
            'error' => 'Error (runtime errors - production)',
            'critical' => 'Critical (critical conditions)',
            'alert' => 'Alert (action required immediately)',
            'emergency' => 'Emergency (system unusable)'
        ];

        $levelChoicesList = [];
        $levelChoiceMap = [];
        $levelIndex = 0;
        $defaultLevelIndex = 0;

        foreach ($levelChoices as $code => $description) {
            $levelChoicesList[] = $description;
            $levelChoiceMap[$levelIndex] = $code;

            if ($code === $defaultLevel) {
                $defaultLevelIndex = $levelIndex;
            }

            $levelIndex++;
        }

        $output->writeln('');
        $levelQuestion = new ChoiceQuestion(
            '? Log level: ',
            $levelChoicesList,
            $defaultLevelIndex
        );
        $selectedLevel = $questionHelper->ask($input, $output, $levelQuestion);

        // Extract code from selected choice
        $logLevel = $defaultLevel;
        foreach ($levelChoiceMap as $idx => $code) {
            if ($levelChoicesList[$idx] === $selectedLevel) {
                $logLevel = $code;
                break;
            }
        }

        return [
            'debugMode' => (bool)$debugMode,
            'logHandler' => $logHandler,
            'logLevel' => $logLevel
        ];
    }
}
