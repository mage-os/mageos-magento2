<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Config;

use MageOS\Installer\Model\Detector\RedisDetector;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

/**
 * Collects Redis configuration interactively
 */
class RedisConfig
{
    public function __construct(
        private readonly RedisDetector $redisDetector
    ) {
    }

    /**
     * Collect Redis configuration
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param QuestionHelper $questionHelper
     * @return array{
     *     session: array{enabled: bool, host: string, port: int, database: int}|null,
     *     cache: array{enabled: bool, host: string, port: int, database: int}|null,
     *     fpc: array{enabled: bool, host: string, port: int, database: int}|null
     * }
     */
    public function collect(
        InputInterface $input,
        OutputInterface $output,
        QuestionHelper $questionHelper
    ): array {
        $output->writeln('');
        $output->writeln('<info>=== Redis Configuration ===</info>');

        // Detect Redis instances
        $output->write('<comment>🔄 Detecting Redis instances...</comment>');
        $detected = $this->redisDetector->detect();

        if (empty($detected)) {
            $output->writeln(' <comment>⚠️</comment>');
            $output->writeln('<comment>⚠️  Redis not detected. Skipping Redis configuration.</comment>');
            $output->writeln('<comment>ℹ️  You can configure Redis manually later in app/etc/env.php</comment>');

            return [
                'session' => null,
                'cache' => null,
                'fpc' => null
            ];
        }

        $output->writeln(' <info>✓</info>');
        $primaryRedis = $detected[0];
        $output->writeln(sprintf(
            '<info>✓ Found Redis on %s:%d</info>',
            $primaryRedis['host'],
            $primaryRedis['port']
        ));

        // Session storage
        $sessionConfig = $this->collectSessionConfig($input, $output, $questionHelper, $primaryRedis);

        // Cache backend
        $cacheConfig = $this->collectCacheConfig($input, $output, $questionHelper, $primaryRedis);

        // Full Page Cache
        $fpcConfig = $this->collectFpcConfig($input, $output, $questionHelper, $primaryRedis);

        return [
            'session' => $sessionConfig,
            'cache' => $cacheConfig,
            'fpc' => $fpcConfig
        ];
    }

    /**
     * Collect session storage configuration
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param QuestionHelper $questionHelper
     * @param array{host: string, port: int} $defaultRedis
     * @return array{enabled: bool, host: string, port: int, database: int}|null
     */
    private function collectSessionConfig(
        InputInterface $input,
        OutputInterface $output,
        QuestionHelper $questionHelper,
        array $defaultRedis
    ): ?array {
        $output->writeln('');
        $enableQuestion = new ConfirmationQuestion(
            '? Use Redis for session storage? [<comment>Y/n</comment>]: ',
            true
        );
        $enabled = $questionHelper->ask($input, $output, $enableQuestion);

        if (!$enabled) {
            return null;
        }

        $hostQuestion = new Question(
            sprintf('? Redis session host [<comment>%s</comment>]: ', $defaultRedis['host']),
            $defaultRedis['host']
        );
        $host = $questionHelper->ask($input, $output, $hostQuestion);

        $portQuestion = new Question(
            sprintf('? Redis session port [<comment>%d</comment>]: ', $defaultRedis['port']),
            (string)$defaultRedis['port']
        );
        $port = (int)$questionHelper->ask($input, $output, $portQuestion);

        $dbQuestion = new Question('? Redis session database [<comment>0</comment>]: ', '0');
        $database = (int)$questionHelper->ask($input, $output, $dbQuestion);

        return [
            'enabled' => true,
            'host' => $host ?? $defaultRedis['host'],
            'port' => $port,
            'database' => $database
        ];
    }

    /**
     * Collect cache backend configuration
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param QuestionHelper $questionHelper
     * @param array{host: string, port: int} $defaultRedis
     * @return array{enabled: bool, host: string, port: int, database: int}|null
     */
    private function collectCacheConfig(
        InputInterface $input,
        OutputInterface $output,
        QuestionHelper $questionHelper,
        array $defaultRedis
    ): ?array {
        $output->writeln('');
        $enableQuestion = new ConfirmationQuestion(
            '? Use Redis for cache backend? [<comment>Y/n</comment>]: ',
            true
        );
        $enabled = $questionHelper->ask($input, $output, $enableQuestion);

        if (!$enabled) {
            return null;
        }

        $hostQuestion = new Question(
            sprintf('? Redis cache host [<comment>%s</comment>]: ', $defaultRedis['host']),
            $defaultRedis['host']
        );
        $host = $questionHelper->ask($input, $output, $hostQuestion);

        $portQuestion = new Question(
            sprintf('? Redis cache port [<comment>%d</comment>]: ', $defaultRedis['port']),
            (string)$defaultRedis['port']
        );
        $port = (int)$questionHelper->ask($input, $output, $portQuestion);

        $dbQuestion = new Question('? Redis cache database [<comment>1</comment>]: ', '1');
        $database = (int)$questionHelper->ask($input, $output, $dbQuestion);

        return [
            'enabled' => true,
            'host' => $host ?? $defaultRedis['host'],
            'port' => $port,
            'database' => $database
        ];
    }

    /**
     * Collect FPC configuration
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param QuestionHelper $questionHelper
     * @param array{host: string, port: int} $defaultRedis
     * @return array{enabled: bool, host: string, port: int, database: int}|null
     */
    private function collectFpcConfig(
        InputInterface $input,
        OutputInterface $output,
        QuestionHelper $questionHelper,
        array $defaultRedis
    ): ?array {
        $output->writeln('');
        $enableQuestion = new ConfirmationQuestion(
            '? Use Redis for Full Page Cache? [<comment>Y/n</comment>]: ',
            true
        );
        $enabled = $questionHelper->ask($input, $output, $enableQuestion);

        if (!$enabled) {
            return null;
        }

        $hostQuestion = new Question(
            sprintf('? Redis FPC host [<comment>%s</comment>]: ', $defaultRedis['host']),
            $defaultRedis['host']
        );
        $host = $questionHelper->ask($input, $output, $hostQuestion);

        $portQuestion = new Question(
            sprintf('? Redis FPC port [<comment>%d</comment>]: ', $defaultRedis['port']),
            (string)$defaultRedis['port']
        );
        $port = (int)$questionHelper->ask($input, $output, $portQuestion);

        $dbQuestion = new Question('? Redis FPC database [<comment>2</comment>]: ', '2');
        $database = (int)$questionHelper->ask($input, $output, $dbQuestion);

        return [
            'enabled' => true,
            'host' => $host ?? $defaultRedis['host'],
            'port' => $port,
            'database' => $database
        ];
    }
}
