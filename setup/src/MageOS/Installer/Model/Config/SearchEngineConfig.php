<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Config;

use MageOS\Installer\Model\Detector\SearchEngineDetector;
use MageOS\Installer\Model\Validator\SearchEngineValidator;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\note;
use function Laravel\Prompts\select;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\text;
use function Laravel\Prompts\warning;

/**
 * Collects search engine configuration with Laravel Prompts
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
     * @return array{engine: string, host: string, port: int, prefix: string}
     */
    public function collect(): array
    {
        while (true) {
            note('Search Engine');

            // Detect search engine
            $detected = spin(
                message: 'Detecting Elasticsearch/OpenSearch...',
                callback: fn () => $this->searchEngineDetector->detect()
            );

            if ($detected) {
                $engineName = match($detected['engine']) {
                    'elasticsearch8' => 'Elasticsearch 8',
                    'elasticsearch7' => 'Elasticsearch 7',
                    'opensearch' => 'OpenSearch',
                    default => $detected['engine']
                };

                info(sprintf('✓ Detected %s on %s:%d', $engineName, $detected['host'], $detected['port']));

                $useDetected = confirm(
                    label: sprintf('Use detected %s?', $engineName),
                    default: true,
                    hint: 'Quick setup with detected configuration'
                );

                if ($useDetected) {
                    $prefix = text(
                        label: 'Index prefix (optional)',
                        default: '',
                        placeholder: 'leave empty for no prefix',
                        required: false
                    );

                    $config = [
                        'engine' => $detected['engine'],
                        'host' => $detected['host'],
                        'port' => $detected['port'],
                        'prefix' => $prefix
                    ];

                    // Test connection
                    if ($this->testConnection($config)) {
                        return $config;
                    }
                    // Failed, retry
                    continue;
                }

                info('Configure manually:');
            } else {
                warning('No search engine detected. Please configure manually.');
            }

            // Manual configuration
            $config = $this->collectManualConfig($detected);

            // Test connection
            if ($this->testConnection($config)) {
                return $config;
            }
            // Failed, retry
        }
    }

    /**
     * Collect search engine config manually
     *
     * @param array<string, mixed>|null $detected
     * @return array{engine: string, host: string, port: int, prefix: string}
     */
    private function collectManualConfig(?array $detected): array
    {
        $defaultEngine = $detected['engine'] ?? 'elasticsearch8';
        $defaultHost = $detected ? sprintf('%s:%d', $detected['host'], $detected['port']) : 'localhost:9200';

        $engine = select(
            label: 'Search engine',
            options: [
                'elasticsearch8' => 'Elasticsearch 8',
                'elasticsearch7' => 'Elasticsearch 7',
                'opensearch' => 'OpenSearch'
            ],
            default: $defaultEngine,
            hint: 'Select the search engine you are using'
        );

        $hostPort = text(
            label: 'Search engine host',
            default: $defaultHost,
            placeholder: 'localhost:9200',
            hint: 'Format: hostname:port'
        );

        // Parse host and port
        $hostParts = explode(':', $hostPort);
        $host = $hostParts[0];
        $port = isset($hostParts[1]) ? (int)$hostParts[1] : 9200;

        $prefix = text(
            label: 'Index prefix (optional)',
            default: '',
            placeholder: 'leave empty for no prefix',
            required: false
        );

        return [
            'engine' => $engine,
            'host' => $host,
            'port' => $port,
            'prefix' => $prefix
        ];
    }

    /**
     * Test search engine connection
     *
     * @param array{engine: string, host: string, port: int, prefix: string} $config
     * @return bool
     */
    private function testConnection(array $config): bool
    {
        $validation = spin(
            message: 'Testing search engine connection...',
            callback: fn () => $this->searchEngineValidator->testConnection(
                $config['engine'],
                $config['host'],
                $config['port']
            )
        );

        if ($validation['success']) {
            info('✓ Search engine connection successful!');
            return true;
        }

        error('Search engine connection failed');
        error($validation['error'] ?? 'Unknown error');
        info('Common issues:');
        info('• Wrong engine type selected (OpenSearch vs Elasticsearch)');
        info('• Service not running or not accessible');
        info('• Firewall blocking the connection');

        $retry = confirm(
            label: 'Search engine connection failed. Do you want to reconfigure?',
            default: true
        );

        if (!$retry) {
            throw new \RuntimeException('Search engine connection test failed. Installation aborted.');
        }

        return false;
    }
}
