<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Detector;

/**
 * Detects Elasticsearch/OpenSearch availability
 */
class SearchEngineDetector
{
    /**
     * @var array<array{host: string, port: int}>
     */
    private array $commonHosts = [
        ['host' => 'localhost', 'port' => 9200],
        ['host' => '127.0.0.1', 'port' => 9200],
        ['host' => 'elasticsearch', 'port' => 9200],
        ['host' => 'opensearch', 'port' => 9200],
    ];

    /**
     * Detect if Elasticsearch/OpenSearch is running
     *
     * @return array{host: string, port: int, version: string|null, engine: string|null}|null
     */
    public function detect(): ?array
    {
        foreach ($this->commonHosts as $hostConfig) {
            $result = $this->checkEndpoint($hostConfig['host'], $hostConfig['port']);
            if ($result !== null) {
                return $result;
            }
        }

        return null;
    }

    /**
     * Check if search engine endpoint is available
     *
     * @param string $host
     * @param int $port
     * @return array{host: string, port: int, version: string|null, engine: string|null}|null
     */
    private function checkEndpoint(string $host, int $port): ?array
    {
        $url = sprintf('http://%s:%d', $host, $port);

        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => 2,
                'ignore_errors' => true
            ]
        ]);

        $response = @file_get_contents($url, false, $context);

        if ($response === false) {
            return null;
        }

        $data = json_decode($response, true);

        if (!is_array($data)) {
            return null;
        }

        $version = $data['version']['number'] ?? null;
        $engine = $this->detectEngine($data);

        return [
            'host' => $host,
            'port' => $port,
            'version' => is_string($version) ? $version : null,
            'engine' => $engine
        ];
    }

    /**
     * Detect if it's Elasticsearch or OpenSearch
     *
     * @param array<string, mixed> $data
     * @return string|null
     */
    private function detectEngine(array $data): ?string
    {
        $distribution = $data['version']['distribution'] ?? null;

        if ($distribution === 'opensearch') {
            return 'opensearch';
        }

        // If no distribution field or it's 'elasticsearch', it's Elasticsearch
        if (!isset($data['version']['distribution']) || $distribution === 'elasticsearch') {
            $version = $data['version']['number'] ?? '';
            if (is_string($version) && str_starts_with($version, '8.')) {
                return 'elasticsearch8';
            } elseif (is_string($version) && str_starts_with($version, '7.')) {
                return 'elasticsearch7';
            }
            return 'elasticsearch8'; // Default to 8
        }

        return null;
    }
}
