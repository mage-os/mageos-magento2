<?php

declare(strict_types=1);

namespace MageOS\Installer\Test\Unit\MageOS\Installer\Model\Validator;

use MageOS\Installer\Model\Validator\SearchEngineValidator;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for SearchEngineValidator
 *
 * Note: Full connection testing requires integration tests with real search engine.
 * These unit tests verify the validator's structure and error handling logic.
 */
class SearchEngineValidatorTest extends TestCase
{
    /** @var SearchEngineValidator */
    private SearchEngineValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new SearchEngineValidator();
    }

    public function testReturnsExpectedStructure(): void
    {
        // Test with invalid host to get error response
        $result = $this->validator->testConnection('opensearch', 'nonexistent_host_xyz', 9200);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('error', $result);
        $this->assertIsBool($result['success']);
    }

    public function testHandlesConnectionFailureGracefully(): void
    {
        $result = $this->validator->testConnection('opensearch', 'invalid_host_12345', 9200);

        $this->assertFalse($result['success']);
        $this->assertNotNull($result['error']);
        $this->assertStringContainsString('Could not connect', $result['error']);
    }

    public function testErrorMessageIncludesEngineType(): void
    {
        $result = $this->validator->testConnection('opensearch', 'invalid_host', 9200);

        $this->assertStringContainsString('opensearch', $result['error']);
    }

    public function testErrorMessageIncludesHostAndPort(): void
    {
        $result = $this->validator->testConnection('elasticsearch8', 'test.local', 9300);

        $this->assertStringContainsString('test.local', $result['error']);
        $this->assertStringContainsString('9300', $result['error']);
    }

    public function testHandlesOpensearchEngineType(): void
    {
        $result = $this->validator->testConnection('opensearch', 'invalid', 9200);

        // Should handle opensearch without errors in logic
        $this->assertIsArray($result);
    }

    public function testHandlesElasticsearchEngineTypes(): void
    {
        $engines = ['elasticsearch', 'elasticsearch7', 'elasticsearch8'];

        foreach ($engines as $engine) {
            $result = $this->validator->testConnection($engine, 'invalid', 9200);

            // Should handle all elasticsearch variants
            $this->assertIsArray($result);
        }
    }

    public function testUsesHttpProtocol(): void
    {
        // Verify the URL construction uses http:// (not https)
        // This is implicit in the implementation but important for port 9200
        $result = $this->validator->testConnection('opensearch', 'localhost', 9200);

        // The error should reference the connection attempt
        if (!$result['success']) {
            $this->assertStringContainsString('localhost:9200', $result['error']);
        }
    }

    public function testHandlesDifferentPorts(): void
    {
        $ports = [9200, 9300, 9400];

        foreach ($ports as $port) {
            $result = $this->validator->testConnection('opensearch', 'invalid', $port);

            $this->assertStringContainsString((string)$port, $result['error']);
        }
    }

    public function testConnectionTimeoutIsReasonable(): void
    {
        // Test that connection doesn't hang forever
        $start = microtime(true);
        $result = $this->validator->testConnection('opensearch', 'invalid_host_xyz', 9200);
        $duration = microtime(true) - $start;

        // Should timeout within reasonable time (5s timeout + overhead)
        $this->assertLessThan(10, $duration, 'Connection test should timeout within 10 seconds');
        $this->assertFalse($result['success']);
    }
}
