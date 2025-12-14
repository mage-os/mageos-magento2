<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Config;

use MageOS\Installer\Model\Detector\SearchEngineDetector;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

/**
 * Collects search engine configuration interactively
 */
class SearchEngineConfig
{
    public function __construct(
        private readonly SearchEngineDetector $searchEngineDetector
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
        $output->writeln('');
        $output->writeln('<info>=== Search Engine ===</info>');

        // Detect search engine
        $output->write('<comment>🔄 Detecting Elasticsearch/OpenSearch...</comment>');
        $detected = $this->searchEngineDetector->detect();

        if ($detected) {
            $output->writeln(' <info>✓</info>');
            $output->writeln(sprintf(
                '<info>✓ Found %s on %s:%d</info>',
                $detected['engine'] ?? 'search engine',
                $detected['host'],
                $detected['port']
            ));

            $defaultEngine = $detected['engine'] ?? 'elasticsearch8';
            $defaultHost = sprintf('%s:%d', $detected['host'], $detected['port']);
        } else {
            $output->writeln(' <comment>⚠️</comment>');
            $output->writeln('<comment>⚠️  No search engine detected. Please configure manually.</comment>');

            $defaultEngine = 'elasticsearch8';
            $defaultHost = 'localhost:9200';
        }

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
}
