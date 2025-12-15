<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Validator;

/**
 * Validates search engine connection
 */
class SearchEngineValidator
{
    /**
     * Test search engine connection
     *
     * @param string $engine
     * @param string $host
     * @param int $port
     * @return array{success: bool, error: string|null}
     */
    public function testConnection(string $engine, string $host, int $port): array
    {
        $url = sprintf('http://%s:%d', $host, $port);

        try {
            $context = stream_context_create([
                'http' => [
                    'method' => 'GET',
                    'timeout' => 5,
                    'ignore_errors' => true
                ]
            ]);

            // phpcs:ignore Magento2.Functions.DiscouragedFunction
            $response = @file_get_contents($url, false, $context);

            if ($response === false) {
                return [
                    'success' => false,
                    'error' => sprintf(
                        'Could not connect to %s at %s:%d. Verify the host and port are correct.',
                        $engine,
                        $host,
                        $port
                    )
                ];
            }

            $data = json_decode($response, true);

            if (!is_array($data)) {
                return [
                    'success' => false,
                    'error' => sprintf(
                        'Service at %s:%d is not responding as a valid search engine',
                        $host,
                        $port
                    )
                ];
            }

            // Validate it's actually the expected engine type
            if ($engine === 'opensearch' && !isset($data['version']['distribution'])) {
                return [
                    'success' => false,
                    'error' => sprintf(
                        'Expected OpenSearch at %s:%d but found Elasticsearch. Please select the correct engine type.',
                        $host,
                        $port
                    )
                ];
            }

            if (str_starts_with($engine, 'elasticsearch') && isset($data['version']['distribution']) && $data['version']['distribution'] === 'opensearch') {
                return [
                    'success' => false,
                    'error' => sprintf(
                        'Expected Elasticsearch at %s:%d but found OpenSearch. Please select "opensearch" as the engine type.',
                        $host,
                        $port
                    )
                ];
            }

            // Test cluster health
            $healthUrl = sprintf('%s/_cluster/health', $url);
            // phpcs:ignore Magento2.Functions.DiscouragedFunction
            $healthResponse = @file_get_contents($healthUrl, false, $context);

            if ($healthResponse !== false) {
                $health = json_decode($healthResponse, true);
                if (is_array($health) && isset($health['status'])) {
                    $status = $health['status'];

                    if ($status === 'red') {
                        return [
                            'success' => false,
                            'error' => sprintf(
                                'Search engine cluster at %s:%d is in RED status. Some primary shards are unassigned.',
                                $host,
                                $port
                            )
                        ];
                    }

                    // Yellow or green is OK
                    return [
                        'success' => true,
                        'error' => null
                    ];
                }
            }

            // If we got a valid response with version info, consider it successful
            if (isset($data['version']['number'])) {
                return [
                    'success' => true,
                    'error' => null
                ];
            }

            return [
                'success' => false,
                'error' => sprintf(
                    'Could not validate search engine at %s:%d. Response is missing version information.',
                    $host,
                    $port
                )
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => sprintf(
                    'Could not connect to search engine: %s',
                    $e->getMessage()
                )
            ];
        }
    }
}
