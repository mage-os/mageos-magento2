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
    public function test_it_constructs_with_null_properties(): void
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

    public function test_it_sets_and_gets_environment(): void
    {
        $context = new InstallationContext();
        $environment = TestDataBuilder::validEnvironmentConfig();

        $context->setEnvironment($environment);

        $this->assertSame($environment, $context->getEnvironment());
    }

    public function test_it_sets_and_gets_database(): void
    {
        $context = new InstallationContext();
        $database = TestDataBuilder::validDatabaseConfig();

        $context->setDatabase($database);

        $this->assertSame($database, $context->getDatabase());
    }

    public function test_it_sets_and_gets_admin(): void
    {
        $context = new InstallationContext();
        $admin = TestDataBuilder::validAdminConfig();

        $context->setAdmin($admin);

        $this->assertSame($admin, $context->getAdmin());
    }

    public function test_it_sets_and_gets_all_configurations(): void
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

    public function test_get_sensitive_fields_returns_password_paths(): void
    {
        $context = new InstallationContext();
        $sensitiveFields = $context->getSensitiveFields();

        $this->assertContains('database.password', $sensitiveFields);
        $this->assertContains('admin.password', $sensitiveFields);
        $this->assertContains('rabbitMQ.password', $sensitiveFields);
        $this->assertContains('email.password', $sensitiveFields);
        $this->assertCount(4, $sensitiveFields);
    }

    public function test_to_array_excludes_sensitive_data(): void
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

    public function test_to_array_includes_created_at_timestamp(): void
    {
        $context = new InstallationContext();
        $array = $context->toArray();

        $this->assertArrayHasKey('_created_at', $array);
        $this->assertMatchesRegularExpression('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $array['_created_at']);
    }

    public function test_to_array_uses_correct_keys_for_serialization(): void
    {
        $context = TestDataBuilder::validInstallationContext();
        $array = $context->toArray();

        // Check key mappings (property name → array key)
        $this->assertArrayHasKey('search', $array); // searchEngine → search
        $this->assertArrayHasKey('rabbitmq', $array); // rabbitMQ → rabbitmq (lowercase)
        $this->assertArrayNotHasKey('searchEngine', $array);
        $this->assertArrayNotHasKey('rabbitMQ', $array);
    }

    public function test_from_array_reconstructs_context(): void
    {
        $data = [
            'environment' => ['type' => 'development', 'mageMode' => 'developer'],
            'database' => ['host' => 'localhost', 'name' => 'magento', 'user' => 'root', 'password' => ''],
            'admin' => ['firstName' => 'John', 'lastName' => 'Doe', 'email' => 'test@test.com', 'username' => 'admin', 'password' => ''],
            'store' => ['baseUrl' => 'https://test.local', 'language' => 'en_US', 'currency' => 'USD', 'timezone' => 'UTC', 'useRewrites' => true],
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

    public function test_from_array_with_partial_data(): void
    {
        $data = [
            'database' => ['host' => 'localhost', 'name' => 'magento', 'user' => 'root', 'password' => '']
        ];

        $context = InstallationContext::fromArray($data);

        $this->assertNotNull($context->getDatabase());
        $this->assertNull($context->getEnvironment());
        $this->assertNull($context->getAdmin());
    }

    public function test_round_trip_preserves_non_sensitive_data(): void
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

    public function test_round_trip_loses_sensitive_data(): void
    {
        $original = TestDataBuilder::validInstallationContext();
        $array = $original->toArray();
        $reconstructed = InstallationContext::fromArray($array);

        // Passwords should be empty after round-trip
        $this->assertEmpty($reconstructed->getDatabase()->password);
        $this->assertEmpty($reconstructed->getAdmin()->password);
    }

    public function test_is_ready_for_installation_returns_false_when_empty(): void
    {
        $context = new InstallationContext();

        $this->assertFalse($context->isReadyForInstallation());
    }

    public function test_is_ready_for_installation_returns_false_with_partial_config(): void
    {
        $context = new InstallationContext();
        $context->setDatabase(TestDataBuilder::validDatabaseConfig());
        $context->setAdmin(TestDataBuilder::validAdminConfig());

        $this->assertFalse($context->isReadyForInstallation());
    }

    public function test_is_ready_for_installation_returns_true_with_minimum_required(): void
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

    public function test_get_missing_passwords_returns_empty_when_all_set(): void
    {
        $context = TestDataBuilder::validInstallationContext();

        $missing = $context->getMissingPasswords();

        $this->assertEmpty($missing);
    }

    public function test_get_missing_passwords_detects_missing_database_password(): void
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

    public function test_get_missing_passwords_detects_missing_admin_password(): void
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

    public function test_get_missing_passwords_only_checks_rabbitmq_when_enabled(): void
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

    public function test_get_missing_passwords_checks_rabbitmq_when_enabled(): void
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

    public function test_get_missing_passwords_only_checks_email_when_configure_and_smtp(): void
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

    public function test_to_array_handles_null_configurations_gracefully(): void
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
