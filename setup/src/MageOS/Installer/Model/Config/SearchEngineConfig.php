<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Config;

use MageOS\Installer\Model\Detector\SearchEngineDetector;
use MageOS\Installer\Model\Validator\SearchEngineValidator;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

/**
 * Collects search engine configuration interactively
 */
class SearchEngineConfig
{
    public function __construct(
        private readonly SearchEngineDetector $searchEngineDetector,
        private readonly SearchEngineValidator $searchEngineValidator
    ) {
    }

    /**
     * Collect search engine configuration
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param QuestionHelper $questionHelper
     * @return array{engine: string, host: string, port: int, prefix: string}
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
                    $output->writeln('<info>=== Search Engine ===</info>');
                } else {
                    $output->writeln('');
                    $output->writeln('<info>=== Search Engine (Retry) ===</info>');
                }

                // Detect search engine
                $output->write('<comment>🔄 Detecting Elasticsearch/OpenSearch...</comment>');
                $detected = $this->searchEngineDetector->detect();

        if ($detected) {
            $output->writeln(' <info>✓</info>');

            // Format engine name nicely
            $engineName = match($detected['engine']) {
                'elasticsearch8' => 'Elasticsearch 8',
                'elasticsearch7' => 'Elasticsearch 7',
                'opensearch' => 'OpenSearch',
                default => $detected['engine'] ?? 'search engine'
            };

            $output->writeln(sprintf(
                '<info>✓ Detected %s on %s:%d</info>',
                $engineName,
                $detected['host'],
                $detected['port']
            ));

            // Ask if user wants to use detected service
            $output->writeln('');
            $useDetectedQuestion = new ConfirmationQuestion(
                sprintf('? Use detected %s? [<comment>Y/n</comment>]: ', $engineName),
                true
            );
            $useDetected = $questionHelper->ask($input, $output, $useDetectedQuestion);

            if ($useDetected) {
                // Just ask for optional prefix
                $prefixQuestion = new Question('? Index prefix (optional): ', '');
                $prefix = $questionHelper->ask($input, $output, $prefixQuestion) ?? '';

                $config = [
                    'engine' => $detected['engine'],
                    'host' => $detected['host'],
                    'port' => $detected['port'],
                    'prefix' => $prefix
                ];

                // Test connection
                if ($this->testSearchEngineConnection($input, $output, $questionHelper, $config)) {
                    return $config;
                }

                // Connection failed, retry
                $isFirstAttempt = false;
                continue;
            }

            // User wants to configure manually
            $output->writeln('<comment>ℹ️  Configure manually:</comment>');
            $output->writeln('');
        } else {
            $output->writeln(' <comment>⚠️</comment>');
            $output->writeln('<comment>⚠️  No search engine detected. Please configure manually.</comment>');
            $output->writeln('');
        }

                // Manual configuration
                $config = $this->collectManualConfig($input, $output, $questionHelper, $detected);

                // Test connection
                if ($this->testSearchEngineConnection($input, $output, $questionHelper, $config)) {
                    return $config;
                }

                // Connection failed, retry
                $isFirstAttempt = false;
            } catch (\RuntimeException $e) {
                // Handle other errors
                throw $e;
            }
        }
    }

    /**
     * Collect search engine config manually
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param QuestionHelper $questionHelper
     * @param array<string, mixed>|null $detected
     * @return array{engine: string, host: string, port: int, prefix: string}
     */
    private function collectManualConfig(
        InputInterface $input,
        OutputInterface $output,
        QuestionHelper $questionHelper,
        ?array $detected
    ): array {
        $defaultEngine = $detected['engine'] ?? 'elasticsearch8';
        $defaultHost = $detected ? sprintf('%s:%d', $detected['host'], $detected['port']) : 'localhost:9200';

        // Search engine choice
        $engineQuestion = new ChoiceQuestion(
            sprintf('? Search engine [<comment>%s</comment>]: ', $defaultEngine),
            ['elasticsearch8', 'elasticsearch7', 'opensearch'],
            $defaultEngine
        );
        $engine = $questionHelper->ask($input, $output, $engineQuestion);

        // Host and port
        $hostQuestion = new Question(
            sprintf('? Search engine host [<comment>%s</comment>]: ', $defaultHost),
            $defaultHost
        );
        $host = $questionHelper->ask($input, $output, $hostQuestion);

        // Parse host and port
        $hostParts = explode(':', $host ?? $defaultHost);
        $finalHost = $hostParts[0];
        $finalPort = isset($hostParts[1]) ? (int)$hostParts[1] : 9200;

        // Index prefix (optional)
        $prefixQuestion = new Question('? Index prefix (optional): ', '');
        $prefix = $questionHelper->ask($input, $output, $prefixQuestion) ?? '';

        return [
            'engine' => $engine ?? $defaultEngine,
            'host' => $finalHost,
            'port' => $finalPort,
            'prefix' => $prefix
        ];
    }

    /**
     * Test search engine connection and handle retry
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param QuestionHelper $questionHelper
     * @param array{engine: string, host: string, port: int, prefix: string} $config
     * @return bool
     */
    private function testSearchEngineConnection(
        InputInterface $input,
        OutputInterface $output,
        QuestionHelper $questionHelper,
        array $config
    ): bool {
        $output->writeln('');
        $output->write('<comment>🔄 Testing search engine connection...</comment>');

        $validation = $this->searchEngineValidator->testConnection(
            $config['engine'],
            $config['host'],
            $config['port']
        );

        if ($validation['success']) {
            $output->writeln(' <info>✓</info>');
            $output->writeln('<info>✓ Search engine connection successful!</info>');
            return true;
        }

        // Connection failed
        $output->writeln(' <error>❌</error>');
        $output->writeln('');
        $output->writeln('<error>❌ ' . $validation['error'] . '</error>');
        $output->writeln('');
        $output->writeln('<comment>Common issues:</comment>');
        $output->writeln('  <comment>• Wrong engine type selected (OpenSearch vs Elasticsearch)</comment>');
        $output->writeln('  <comment>• Service not running or not accessible</comment>');
        $output->writeln('  <comment>• Firewall blocking the connection</comment>');
        $output->writeln('  <comment>• Authentication required but not configured</comment>');

        // Ask if user wants to retry
        $retryQuestion = new ConfirmationQuestion(
            "\n<question>? Search engine connection failed. Do you want to reconfigure?</question> [<comment>Y/n</comment>]: ",
            true
        );
        $retry = $questionHelper->ask($input, $output, $retryQuestion);

        if (!$retry) {
            throw new \RuntimeException('Search engine connection test failed. Installation aborted.');
        }

        return false;
    }
}
