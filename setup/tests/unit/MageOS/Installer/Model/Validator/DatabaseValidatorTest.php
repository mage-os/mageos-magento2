<?php

declare(strict_types=1);

namespace MageOS\Installer\Test\Unit\MageOS\Installer\Model\Validator;

use MageOS\Installer\Model\Validator\DatabaseValidator;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for DatabaseValidator
 *
 * Note: Connection testing (validate method) requires integration tests with real database.
 * These unit tests focus on validation logic that doesn't require external dependencies.
 */
class DatabaseValidatorTest extends TestCase
{
    private DatabaseValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new DatabaseValidator();
    }

    /**
     * @dataProvider validDatabaseNameProvider
     */
    public function test_accepts_valid_database_names(string $name): void
    {
        $result = $this->validator->validateDatabaseName($name);

        $this->assertTrue($result['valid'], "Database name '{$name}' should be valid");
        $this->assertNull($result['error']);
    }

    /**
     * @dataProvider invalidDatabaseNameProvider
     */
    public function test_rejects_invalid_database_names(string $name, string $expectedError): void
    {
        $result = $this->validator->validateDatabaseName($name);

        $this->assertFalse($result['valid']);
        $this->assertEquals($expectedError, $result['error']);
    }

    public function test_rejects_empty_database_name(): void
    {
        $result = $this->validator->validateDatabaseName('');

        $this->assertFalse($result['valid']);
        $this->assertEquals('Database name cannot be empty', $result['error']);
    }

    public function test_rejects_database_name_with_spaces(): void
    {
        $result = $this->validator->validateDatabaseName('my database');

        $this->assertFalse($result['valid']);
        $this->assertStringContainsString('letters, numbers, underscores, and hyphens', $result['error']);
    }

    public function test_rejects_database_name_with_special_characters(): void
    {
        $result = $this->validator->validateDatabaseName('db@name');

        $this->assertFalse($result['valid']);
        $this->assertStringContainsString('letters, numbers, underscores, and hyphens', $result['error']);
    }

    public function test_rejects_database_name_with_sql_injection_attempt(): void
    {
        $result = $this->validator->validateDatabaseName("db'; DROP TABLE users;--");

        $this->assertFalse($result['valid']);
        $this->assertStringContainsString('letters, numbers, underscores, and hyphens', $result['error']);
    }

    public function test_validate_returns_expected_structure_on_success(): void
    {
        // This tests that the validate method returns the correct array structure
        // Actual connection testing requires integration tests with real database
        $result = $this->validator->validate('nonexistent_host', 'db', 'user', 'pass');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('error', $result);
        $this->assertIsBool($result['success']);
    }

    public function test_validate_handles_connection_errors_gracefully(): void
    {
        // Test with invalid host to trigger connection error
        $result = $this->validator->validate('invalid_host_12345', 'db', 'user', 'pass');

        $this->assertFalse($result['success']);
        $this->assertNotNull($result['error']);
        $this->assertStringContainsString('Database connection failed', $result['error']);
    }

    public static function validDatabaseNameProvider(): array
    {
        return [
            'simple' => ['magento'],
            'with underscore' => ['magento_db'],
            'with hyphen' => ['magento-db'],
            'with numbers' => ['magento2'],
            'all valid chars' => ['magento_2-db'],
            'uppercase' => ['MAGENTO'],
            'mixed case' => ['MagentoDb'],
            'starting with number' => ['2magento'],
        ];
    }

    public static function invalidDatabaseNameProvider(): array
    {
        return [
            'empty' => ['', 'Database name cannot be empty'],
            'with space' => ['my database', 'Database name can only contain letters, numbers, underscores, and hyphens'],
            'with dot' => ['magento.db', 'Database name can only contain letters, numbers, underscores, and hyphens'],
            'with slash' => ['magento/db', 'Database name can only contain letters, numbers, underscores, and hyphens'],
            'with special char' => ['magento@db', 'Database name can only contain letters, numbers, underscores, and hyphens'],
            'sql injection' => ["'; DROP TABLE", 'Database name can only contain letters, numbers, underscores, and hyphens'],
        ];
    }
}
