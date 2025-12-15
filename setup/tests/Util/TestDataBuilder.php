<?php

declare(strict_types=1);

namespace MageOS\Installer\Test\Util;

use MageOS\Installer\Model\InstallationContext;
use MageOS\Installer\Model\VO\AdminConfiguration;
use MageOS\Installer\Model\VO\BackendConfiguration;
use MageOS\Installer\Model\VO\CronConfiguration;
use MageOS\Installer\Model\VO\DatabaseConfiguration;
use MageOS\Installer\Model\VO\EmailConfiguration;
use MageOS\Installer\Model\VO\EnvironmentConfiguration;
use MageOS\Installer\Model\VO\LoggingConfiguration;
use MageOS\Installer\Model\VO\RabbitMQConfiguration;
use MageOS\Installer\Model\VO\RedisConfiguration;
use MageOS\Installer\Model\VO\SampleDataConfiguration;
use MageOS\Installer\Model\VO\SearchEngineConfiguration;
use MageOS\Installer\Model\VO\StoreConfiguration;
use MageOS\Installer\Model\VO\ThemeConfiguration;

/**
 * Test data builder for creating fixture instances
 *
 * Provides convenient methods to create valid test data for all VOs
 */
final class TestDataBuilder
{
    /**
     * Create a valid DatabaseConfiguration for testing
     */
    public static function validDatabaseConfig(): DatabaseConfiguration
    {
        return new DatabaseConfiguration(
            host: 'localhost',
            name: 'magento_test',
            user: 'magento_user',
            password: 'SecureP@ss123',
            prefix: 'mg_'
        );
    }

    /**
     * Create a minimal DatabaseConfiguration (empty prefix)
     */
    public static function minimalDatabaseConfig(): DatabaseConfiguration
    {
        return new DatabaseConfiguration(
            host: 'localhost',
            name: 'magento',
            user: 'root',
            password: 'password'
        );
    }

    /**
     * Create a valid AdminConfiguration for testing
     */
    public static function validAdminConfig(): AdminConfiguration
    {
        return new AdminConfiguration(
            firstName: 'John',
            lastName: 'Doe',
            email: 'admin@example.com',
            username: 'admin',
            password: 'Admin123!'
        );
    }

    /**
     * Create a valid EnvironmentConfiguration for testing
     */
    public static function validEnvironmentConfig(): EnvironmentConfiguration
    {
        return new EnvironmentConfiguration(
            type: 'development',
            mageMode: 'developer'
        );
    }

    /**
     * Create a valid StoreConfiguration for testing
     */
    public static function validStoreConfig(): StoreConfiguration
    {
        return new StoreConfiguration(
            baseUrl: 'https://magento.local',
            language: 'en_US',
            currency: 'USD',
            timezone: 'America/Los_Angeles',
            useRewrites: true
        );
    }

    /**
     * Create a valid BackendConfiguration for testing
     */
    public static function validBackendConfig(): BackendConfiguration
    {
        return new BackendConfiguration(
            frontname: 'admin'
        );
    }

    /**
     * Create a valid SearchEngineConfiguration for testing
     */
    public static function validSearchEngineConfig(): SearchEngineConfiguration
    {
        return new SearchEngineConfiguration(
            engine: 'opensearch',
            host: 'localhost',
            port: 9200,
            prefix: 'magento'
        );
    }

    /**
     * Create a valid RedisConfiguration for testing
     */
    public static function validRedisConfig(): RedisConfiguration
    {
        return new RedisConfiguration(
            session: true,
            cache: true,
            fpc: true,
            host: 'localhost',
            port: 6379,
            sessionDb: 0,
            cacheDb: 1,
            fpcDb: 2
        );
    }

    /**
     * Create a valid RabbitMQConfiguration for testing
     */
    public static function validRabbitMQConfig(): RabbitMQConfiguration
    {
        return new RabbitMQConfiguration(
            enabled: true,
            host: 'localhost',
            port: 5672,
            user: 'guest',
            password: 'guest',
            virtualHost: '/'
        );
    }

    /**
     * Create a valid LoggingConfiguration for testing
     */
    public static function validLoggingConfig(): LoggingConfiguration
    {
        return new LoggingConfiguration(
            debugMode: false,
            logLevel: 'error'
        );
    }

    /**
     * Create a valid SampleDataConfiguration for testing
     */
    public static function validSampleDataConfig(): SampleDataConfiguration
    {
        return new SampleDataConfiguration(
            install: true
        );
    }

    /**
     * Create a valid ThemeConfiguration for testing
     */
    public static function validThemeConfig(): ThemeConfiguration
    {
        return new ThemeConfiguration(
            install: true,
            theme: 'hyva-default'
        );
    }

    /**
     * Create a valid CronConfiguration for testing
     */
    public static function validCronConfig(): CronConfiguration
    {
        return new CronConfiguration(
            configure: true
        );
    }

    /**
     * Create a valid EmailConfiguration for testing
     */
    public static function validEmailConfig(): EmailConfiguration
    {
        return new EmailConfiguration(
            configure: true,
            transport: 'smtp',
            host: 'smtp.example.com',
            port: 587,
            auth: 'login',
            username: 'user@example.com',
            password: 'EmailP@ss123'
        );
    }

    /**
     * Create a fully populated InstallationContext for testing
     */
    public static function validInstallationContext(): InstallationContext
    {
        $context = new InstallationContext();
        $context->setEnvironment(self::validEnvironmentConfig());
        $context->setDatabase(self::validDatabaseConfig());
        $context->setAdmin(self::validAdminConfig());
        $context->setStore(self::validStoreConfig());
        $context->setBackend(self::validBackendConfig());
        $context->setSearchEngine(self::validSearchEngineConfig());
        $context->setRedis(self::validRedisConfig());
        $context->setRabbitMQ(self::validRabbitMQConfig());
        $context->setLogging(self::validLoggingConfig());
        $context->setSampleData(self::validSampleDataConfig());
        $context->setTheme(self::validThemeConfig());
        $context->setCron(self::validCronConfig());
        $context->setEmail(self::validEmailConfig());

        return $context;
    }

    /**
     * Create a minimal InstallationContext (only required fields)
     */
    public static function minimalInstallationContext(): InstallationContext
    {
        $context = new InstallationContext();
        $context->setDatabase(self::minimalDatabaseConfig());
        $context->setAdmin(self::validAdminConfig());
        $context->setStore(self::validStoreConfig());

        return $context;
    }
}
