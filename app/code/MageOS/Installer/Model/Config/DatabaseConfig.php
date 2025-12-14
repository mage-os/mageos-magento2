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
        $output->writeln('');
        $output->writeln('<info>=== Database Configuration ===</info>');

        // Detect database
        $detected = $this->databaseDetector->detect();
        $defaultHost = $detected ? $detected['host'] : 'localhost';

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
            throw new \RuntimeException('Database connection test failed');
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
    }
}
