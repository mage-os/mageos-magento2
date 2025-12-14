<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Config;

use MageOS\Installer\Model\Detector\DatabaseDetector;
use MageOS\Installer\Model\Validator\DatabaseValidator;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

/**
 * Collects database configuration interactively
 */
class DatabaseConfig
{
    public function __construct(
        private readonly DatabaseDetector $databaseDetector,
        private readonly DatabaseValidator $databaseValidator
    ) {
    }

    /**
     * Collect database configuration
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param QuestionHelper $questionHelper
     * @return array{host: string, name: string, user: string, password: string, prefix: string}
     */
    public function collect(
        InputInterface $input,
        OutputInterface $output,
        QuestionHelper $questionHelper
    ): array {
        $isFirstAttempt = true;

        while (true) {
            try {
                if ($isFirstAttempt) {
                    $output->writeln('');
                    $output->writeln('<info>=== Database Configuration ===</info>');
                } else {
                    $output->writeln('');
                    $output->writeln('<info>=== Database Configuration (Retry) ===</info>');
                }

                // Detect database
                $output->write('<comment>🔄 Detecting MySQL/MariaDB...</comment>');
                $detected = $this->databaseDetector->detect();

                if ($detected) {
                    $output->writeln(' <info>✓</info>');
                    $output->writeln(sprintf(
                        '<info>✓ Detected database on %s:%d</info>',
                        $detected['host'],
                        $detected['port']
                    ));
                    $defaultHost = $detected['host'];
                } else {
                    $output->writeln(' <comment>⚠️</comment>');
                    $output->writeln('<comment>⚠️  No database detected on common ports</comment>');
                    $defaultHost = 'localhost';
                }

                $output->writeln('');

                // Database host
                $hostQuestion = new Question(
                    sprintf('? Database host [<comment>%s</comment>]: ', $defaultHost),
                    $defaultHost
                );
                $host = $questionHelper->ask($input, $output, $hostQuestion);

                // Database name
                $dbNameQuestion = new Question('? Database name [<comment>magento</comment>]: ', 'magento');
                $dbNameQuestion->setValidator(function ($answer) {
                    $result = $this->databaseValidator->validateDatabaseName($answer ?? '');
                    if (!$result['valid']) {
                        throw new \RuntimeException($result['error'] ?? 'Invalid database name');
                    }
                    return $answer;
                });
                $name = $questionHelper->ask($input, $output, $dbNameQuestion);

                // Database user
                $userQuestion = new Question('? Database user [<comment>root</comment>]: ', 'root');
                $user = $questionHelper->ask($input, $output, $userQuestion);

                // Database password
                $passwordQuestion = new Question('? Database password: ');
                $passwordQuestion->setHidden(true);
                $passwordQuestion->setHiddenFallback(false);
                $password = $questionHelper->ask($input, $output, $passwordQuestion) ?? '';

                // Table prefix (optional)
                $prefixQuestion = new Question('? Table prefix (optional): ', '');
                $prefix = $questionHelper->ask($input, $output, $prefixQuestion) ?? '';

                // Test database connection
                $output->writeln('');
                $output->write('<comment>🔄 Testing database connection...</comment>');

                $validationResult = $this->databaseValidator->validate(
                    $host ?? $defaultHost,
                    $name ?? 'magento',
                    $user ?? 'root',
                    $password
                );

                if (!$validationResult['success']) {
                    $output->writeln('');
                    $output->writeln('<error>❌ ' . $validationResult['error'] . '</error>');

                    // Ask if user wants to retry
                    $retryQuestion = new ConfirmationQuestion(
                        "\n<question>? Database connection failed. Do you want to reconfigure?</question> [<comment>Y/n</comment>]: ",
                        true
                    );
                    $retry = $questionHelper->ask($input, $output, $retryQuestion);

                    if (!$retry) {
                        throw new \RuntimeException('Database connection test failed. Installation aborted.');
                    }

                    // Continue loop to retry
                    $isFirstAttempt = false;
                    continue;
                }

                $output->writeln(' <info>✓</info>');
                $output->writeln('<info>✓ Database connection successful!</info>');

                return [
                    'host' => $host ?? $defaultHost,
                    'name' => $name ?? 'magento',
                    'user' => $user ?? 'root',
                    'password' => $password,
                    'prefix' => $prefix
                ];
            } catch (\RuntimeException $e) {
                // If it's a validation error (not connection), ask to retry
                if (str_contains($e->getMessage(), 'Database connection')) {
                    throw $e; // Re-throw connection errors that already asked for retry
                }

                // For other validation errors, show and ask to retry
                $output->writeln('');
                $output->writeln('<error>❌ ' . $e->getMessage() . '</error>');

                $retryQuestion = new ConfirmationQuestion(
                    "\n<question>? Validation failed. Do you want to try again?</question> [<comment>Y/n</comment>]: ",
                    true
                );
                $retry = $questionHelper->ask($input, $output, $retryQuestion);

                if (!$retry) {
                    throw new \RuntimeException('Database configuration failed. Installation aborted.');
                }

                $isFirstAttempt = false;
            }
        }
    }
}
