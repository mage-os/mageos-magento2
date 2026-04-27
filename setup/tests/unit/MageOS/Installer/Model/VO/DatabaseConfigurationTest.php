<?php

declare(strict_types=1);

namespace MageOS\Installer\Test\Unit\MageOS\Installer\Model\VO;

use MageOS\Installer\Model\VO\DatabaseConfiguration;
use MageOS\Installer\Test\TestCase\AbstractVOTest;

/**
 * Unit tests for DatabaseConfiguration VO
 *
 * Reference implementation demonstrating the test pattern for all VOs
 */
class DatabaseConfigurationTest extends AbstractVOTest
{
    protected function createValidInstance(): DatabaseConfiguration
    {
        return new DatabaseConfiguration(
            host: 'localhost',
            name: 'magento_test',
            user: 'magento_user',
            password: 'SecureP@ss123',
            prefix: 'mg_'
        );
    }

    protected function getSensitiveFields(): array
    {
        return ['password'];
    }

    /**
     * Test construction with all parameters
     */
    public function testItConstructsWithAllParameters(): void
    {
        $config = new DatabaseConfiguration(
            host: 'db.example.com',
            name: 'magento',
            user: 'magento_user',
            password: 'SecurePassword!',
            prefix: 'mg_'
        );

        $this->assertPropertyEquals($config, 'host', 'db.example.com');
        $this->assertPropertyEquals($config, 'name', 'magento');
        $this->assertPropertyEquals($config, 'user', 'magento_user');
        $this->assertPropertyEquals($config, 'password', 'SecurePassword!');
        $this->assertPropertyEquals($config, 'prefix', 'mg_');
    }

    /**
     * Test construction with default prefix
     */
    public function testItConstructsWithDefaultPrefix(): void
    {
        $config = new DatabaseConfiguration(
            host: 'localhost',
            name: 'magento',
            user: 'root',
            password: 'password'
        );

        $this->assertPropertyEquals($config, 'prefix', '');
    }

    /**
     * Test toArray() contains all non-sensitive fields
     */
    public function testToArrayContainsAllNonSensitiveFields(): void
    {
        $config = $this->createValidInstance();
        $array = $config->toArray(includeSensitive: false);

        $this->assertArrayHasKey('host', $array);
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('user', $array);
        $this->assertArrayHasKey('prefix', $array);
        $this->assertArrayNotHasKey('password', $array);
    }

    /**
     * Test toArray() with includeSensitive=true
     */
    public function testToArrayWithSensitiveIncludesPassword(): void
    {
        $config = $this->createValidInstance();
        $array = $config->toArray(includeSensitive: true);

        $this->assertArrayHasKey('password', $array);
        $this->assertEquals('SecureP@ss123', $array['password']);
    }

    /**
     * Test fromArray() with complete data
     */
    public function testFromArrayWithCompleteData(): void
    {
        $data = [
            'host' => 'db.local',
            'name' => 'magento_db',
            'user' => 'db_user',
            'password' => 'DbPass123',
            'prefix' => 'mage_'
        ];

        $config = DatabaseConfiguration::fromArray($data);

        $this->assertPropertyEquals($config, 'host', 'db.local');
        $this->assertPropertyEquals($config, 'name', 'magento_db');
        $this->assertPropertyEquals($config, 'user', 'db_user');
        $this->assertPropertyEquals($config, 'password', 'DbPass123');
        $this->assertPropertyEquals($config, 'prefix', 'mage_');
    }

    /**
     * Test fromArray() with missing optional fields
     */
    public function testFromArrayWithMissingOptionalFields(): void
    {
        $data = [
            'host' => 'localhost',
            'name' => 'magento',
            'user' => 'root',
            'password' => 'pass'
        ];

        $config = DatabaseConfiguration::fromArray($data);

        $this->assertPropertyEquals($config, 'prefix', '');
    }

    /**
     * Test fromArray() with missing required fields uses empty strings
     */
    public function testFromArrayWithMissingRequiredFields(): void
    {
        $data = ['host' => 'localhost'];

        $config = DatabaseConfiguration::fromArray($data);

        $this->assertPropertyEquals($config, 'host', 'localhost');
        $this->assertPropertyEquals($config, 'name', '');
        $this->assertPropertyEquals($config, 'user', '');
        $this->assertPropertyEquals($config, 'password', '');
    }

    /**
     * Test fromArray() with extra fields ignores them
     */
    public function testFromArrayIgnoresExtraFields(): void
    {
        $data = [
            'host' => 'localhost',
            'name' => 'magento',
            'user' => 'root',
            'password' => 'pass',
            'port' => '3306', // extra field
            'charset' => 'utf8mb4' // extra field
        ];

        $config = DatabaseConfiguration::fromArray($data);

        // Should not throw, extra fields ignored
        $this->assertInstanceOf(DatabaseConfiguration::class, $config);
    }

    /**
     * Test round-trip with sensitive data
     */
    public function testRoundTripWithSensitiveData(): void
    {
        $original = $this->createValidInstance();
        $array = $original->toArray(includeSensitive: true);
        $reconstructed = DatabaseConfiguration::fromArray($array);

        $this->assertEquals($original, $reconstructed);
    }

    /**
     * Test round-trip without sensitive data loses password
     */
    public function testRoundTripWithoutSensitiveLosesPassword(): void
    {
        $original = $this->createValidInstance();
        $array = $original->toArray(includeSensitive: false);
        $reconstructed = DatabaseConfiguration::fromArray($array);

        $this->assertPropertyEquals($reconstructed, 'password', '');
        $this->assertNotEquals($original, $reconstructed);
    }
}
