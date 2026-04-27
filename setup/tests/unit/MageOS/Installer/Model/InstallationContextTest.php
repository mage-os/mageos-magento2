<?php

declare(strict_types=1);

namespace MageOS\Installer\Test\Unit\MageOS\Installer\Model;

use MageOS\Installer\Model\InstallationContext;
use MageOS\Installer\Test\Util\TestDataBuilder;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for InstallationContext
 *
 * Tests the central data container that orchestrates all VOs
 */
class InstallationContextTest extends TestCase
{
    public function testItConstructsWithNullProperties(): void
    {
        $context = new InstallationContext();

        $this->assertNull($context->getEnvironment());
        $this->assertNull($context->getDatabase());
        $this->assertNull($context->getAdmin());
        $this->assertNull($context->getStore());
        $this->assertNull($context->getBackend());
        $this->assertNull($context->getSearchEngine());
        $this->assertNull($context->getRedis());
        $this->assertNull($context->getRabbitMQ());
        $this->assertNull($context->getLogging());
        $this->assertNull($context->getSampleData());
        $this->assertNull($context->getTheme());
        $this->assertNull($context->getCron());
        $this->assertNull($context->getEmail());
    }

    public function testItSetsAndGetsEnvironment(): void
    {
        $context = new InstallationContext();
        $environment = TestDataBuilder::validEnvironmentConfig();

        $context->setEnvironment($environment);

        $this->assertSame($environment, $context->getEnvironment());
    }

    public function testItSetsAndGetsDatabase(): void
    {
        $context = new InstallationContext();
        $database = TestDataBuilder::validDatabaseConfig();

        $context->setDatabase($database);

        $this->assertSame($database, $context->getDatabase());
    }

    public function testItSetsAndGetsAdmin(): void
    {
        $context = new InstallationContext();
        $admin = TestDataBuilder::validAdminConfig();

        $context->setAdmin($admin);

        $this->assertSame($admin, $context->getAdmin());
    }

    public function testItSetsAndGetsAllConfigurations(): void
    {
        $context = TestDataBuilder::validInstallationContext();

        $this->assertNotNull($context->getEnvironment());
        $this->assertNotNull($context->getDatabase());
        $this->assertNotNull($context->getAdmin());
        $this->assertNotNull($context->getStore());
        $this->assertNotNull($context->getBackend());
        $this->assertNotNull($context->getSearchEngine());
        $this->assertNotNull($context->getRedis());
        $this->assertNotNull($context->getRabbitMQ());
        $this->assertNotNull($context->getLogging());
        $this->assertNotNull($context->getSampleData());
        $this->assertNotNull($context->getTheme());
        $this->assertNotNull($context->getCron());
        $this->assertNotNull($context->getEmail());
    }

    public function testGetSensitiveFieldsReturnsPasswordPaths(): void
    {
        $context = new InstallationContext();
        $sensitiveFields = $context->getSensitiveFields();

        $this->assertContains('database.password', $sensitiveFields);
        $this->assertContains('admin.password', $sensitiveFields);
        $this->assertContains('rabbitMQ.password', $sensitiveFields);
        $this->assertContains('email.password', $sensitiveFields);
        $this->assertCount(4, $sensitiveFields);
    }

    public function testToArrayExcludesSensitiveData(): void
    {
        $context = TestDataBuilder::validInstallationContext();
        $array = $context->toArray();

        // Should have all non-sensitive config
        $this->assertArrayHasKey('environment', $array);
        $this->assertArrayHasKey('database', $array);
        $this->assertArrayHasKey('admin', $array);
        $this->assertArrayHasKey('store', $array);

        // Database should not have password
        $this->assertArrayNotHasKey('password', $array['database']);

        // Admin should not have password
        $this->assertArrayNotHasKey('password', $array['admin']);
    }

    public function testToArrayIncludesCreatedAtTimestamp(): void
    {
        $context = new InstallationContext();
        $array = $context->toArray();

        $this->assertArrayHasKey('_created_at', $array);
        $this->assertMatchesRegularExpression('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $array['_created_at']);
    }

    public function testToArrayUsesCorrectKeysForSerialization(): void
    {
        $context = TestDataBuilder::validInstallationContext();
        $array = $context->toArray();

        // Check key mappings (property name → array key)
        $this->assertArrayHasKey('search', $array); // searchEngine → search
        $this->assertArrayHasKey('rabbitmq', $array); // rabbitMQ → rabbitmq (lowercase)
        $this->assertArrayNotHasKey('searchEngine', $array);
        $this->assertArrayNotHasKey('rabbitMQ', $array);
    }

    public function testFromArrayReconstructsContext(): void
    {
        $data = [
            'environment' => ['type' => 'development', 'mageMode' => 'developer'],
            'database' => ['host' => 'localhost', 'name' => 'magento', 'user' => 'root', 'password' => ''],
            'admin' => [
                'firstName' => 'John', 'lastName' => 'Doe', 'email' => 'test@test.com',
                'username' => 'admin', 'password' => '',
            ],
            'store' => [
                'baseUrl' => 'https://test.local', 'language' => 'en_US',
                'currency' => 'USD', 'timezone' => 'UTC', 'useRewrites' => true,
            ],
            'backend' => ['frontname' => 'admin'],
            'search' => ['engine' => 'opensearch', 'host' => 'localhost', 'port' => 9200, 'prefix' => ''],
            'logging' => ['debugMode' => false, 'logLevel' => 'error']
        ];

        $context = InstallationContext::fromArray($data);

        $this->assertNotNull($context->getEnvironment());
        $this->assertNotNull($context->getDatabase());
        $this->assertNotNull($context->getAdmin());
        $this->assertNotNull($context->getStore());
        $this->assertNotNull($context->getBackend());
        $this->assertNotNull($context->getSearchEngine());
        $this->assertNotNull($context->getLogging());
    }

    public function testFromArrayWithPartialData(): void
    {
        $data = [
            'database' => ['host' => 'localhost', 'name' => 'magento', 'user' => 'root', 'password' => '']
        ];

        $context = InstallationContext::fromArray($data);

        $this->assertNotNull($context->getDatabase());
        $this->assertNull($context->getEnvironment());
        $this->assertNull($context->getAdmin());
    }

    public function testRoundTripPreservesNonSensitiveData(): void
    {
        $original = TestDataBuilder::validInstallationContext();
        $array = $original->toArray();
        $reconstructed = InstallationContext::fromArray($array);

        // Non-sensitive data should be preserved
        $this->assertEquals(
            $original->getEnvironment()->type,
            $reconstructed->getEnvironment()->type
        );
        $this->assertEquals(
            $original->getDatabase()->host,
            $reconstructed->getDatabase()->host
        );
        $this->assertEquals(
            $original->getStore()->baseUrl,
            $reconstructed->getStore()->baseUrl
        );
    }

    public function testRoundTripLosesSensitiveData(): void
    {
        $original = TestDataBuilder::validInstallationContext();
        $array = $original->toArray();
        $reconstructed = InstallationContext::fromArray($array);

        // Passwords should be empty after round-trip
        $this->assertEmpty($reconstructed->getDatabase()->password);
        $this->assertEmpty($reconstructed->getAdmin()->password);
    }

    public function testIsReadyForInstallationReturnsFalseWhenEmpty(): void
    {
        $context = new InstallationContext();

        $this->assertFalse($context->isReadyForInstallation());
    }

    public function testIsReadyForInstallationReturnsFalseWithPartialConfig(): void
    {
        $context = new InstallationContext();
        $context->setDatabase(TestDataBuilder::validDatabaseConfig());
        $context->setAdmin(TestDataBuilder::validAdminConfig());

        $this->assertFalse($context->isReadyForInstallation());
    }

    public function testIsReadyForInstallationReturnsTrueWithMinimumRequired(): void
    {
        $context = new InstallationContext();
        $context->setEnvironment(TestDataBuilder::validEnvironmentConfig());
        $context->setDatabase(TestDataBuilder::validDatabaseConfig());
        $context->setAdmin(TestDataBuilder::validAdminConfig());
        $context->setStore(TestDataBuilder::validStoreConfig());
        $context->setBackend(TestDataBuilder::validBackendConfig());
        $context->setSearchEngine(TestDataBuilder::validSearchEngineConfig());
        $context->setLogging(TestDataBuilder::validLoggingConfig());

        $this->assertTrue($context->isReadyForInstallation());
    }

    public function testGetMissingPasswordsReturnsEmptyWhenAllSet(): void
    {
        $context = TestDataBuilder::validInstallationContext();

        $missing = $context->getMissingPasswords();

        $this->assertEmpty($missing);
    }

    public function testGetMissingPasswordsDetectsMissingDatabasePassword(): void
    {
        $context = new InstallationContext();
        $database = new \MageOS\Installer\Model\VO\DatabaseConfiguration(
            host: 'localhost',
            name: 'magento',
            user: 'root',
            password: '' // Empty password
        );
        $context->setDatabase($database);

        $missing = $context->getMissingPasswords();

        $this->assertContains('database.password', $missing);
    }

    public function testGetMissingPasswordsDetectsMissingAdminPassword(): void
    {
        $context = new InstallationContext();
        $admin = TestDataBuilder::validAdminConfig();
        // Create admin with empty password
        $adminNoPass = new \MageOS\Installer\Model\VO\AdminConfiguration(
            firstName: $admin->firstName,
            lastName: $admin->lastName,
            email: $admin->email,
            username: $admin->username,
            password: ''
        );
        $context->setAdmin($adminNoPass);

        $missing = $context->getMissingPasswords();

        $this->assertContains('admin.password', $missing);
    }

    public function testGetMissingPasswordsOnlyChecksRabbitmqWhenEnabled(): void
    {
        $context = new InstallationContext();
        $rabbitMQ = new \MageOS\Installer\Model\VO\RabbitMQConfiguration(
            enabled: false,
            password: '' // Empty but disabled
        );
        $context->setRabbitMQ($rabbitMQ);

        $missing = $context->getMissingPasswords();

        $this->assertNotContains('rabbitMQ.password', $missing);
    }

    public function testGetMissingPasswordsChecksRabbitmqWhenEnabled(): void
    {
        $context = new InstallationContext();
        $rabbitMQ = new \MageOS\Installer\Model\VO\RabbitMQConfiguration(
            enabled: true,
            password: '' // Empty and enabled
        );
        $context->setRabbitMQ($rabbitMQ);

        $missing = $context->getMissingPasswords();

        $this->assertContains('rabbitMQ.password', $missing);
    }

    public function testGetMissingPasswordsOnlyChecksEmailWhenConfigureAndSmtp(): void
    {
        $context = new InstallationContext();

        // Case 1: Not configured
        $emailNotConfigured = new \MageOS\Installer\Model\VO\EmailConfiguration(
            configure: false,
            transport: 'smtp',
            password: ''
        );
        $context->setEmail($emailNotConfigured);
        $this->assertNotContains('email.password', $context->getMissingPasswords());

        // Case 2: Configured but sendmail
        $emailSendmail = new \MageOS\Installer\Model\VO\EmailConfiguration(
            configure: true,
            transport: 'sendmail',
            password: ''
        );
        $context->setEmail($emailSendmail);
        $this->assertNotContains('email.password', $context->getMissingPasswords());

        // Case 3: Configured and SMTP with empty password
        $emailSmtp = new \MageOS\Installer\Model\VO\EmailConfiguration(
            configure: true,
            transport: 'smtp',
            password: ''
        );
        $context->setEmail($emailSmtp);
        $this->assertContains('email.password', $context->getMissingPasswords());
    }

    public function testToArrayHandlesNullConfigurationsGracefully(): void
    {
        $context = new InstallationContext();
        $context->setDatabase(TestDataBuilder::validDatabaseConfig());
        // Leave other configs null

        $array = $context->toArray();

        $this->assertArrayHasKey('database', $array);
        $this->assertArrayNotHasKey('admin', $array);
        $this->assertArrayNotHasKey('environment', $array);
    }
}
