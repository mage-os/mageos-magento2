<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model;

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
 * Installation context holding all configuration
 *
 * This replaces passing 10+ array parameters between methods.
 * Provides type-safe access to configuration and handles
 * serialization/deserialization (excluding sensitive data).
 */
class InstallationContext
{
    private ?EnvironmentConfiguration $environment = null;
    private ?DatabaseConfiguration $database = null;
    private ?AdminConfiguration $admin = null;
    private ?StoreConfiguration $store = null;
    private ?BackendConfiguration $backend = null;
    private ?SearchEngineConfiguration $searchEngine = null;
    private ?RedisConfiguration $redis = null;
    private ?RabbitMQConfiguration $rabbitMQ = null;
    private ?LoggingConfiguration $logging = null;
    private ?SampleDataConfiguration $sampleData = null;
    private ?ThemeConfiguration $theme = null;
    private ?CronConfiguration $cron = null;
    private ?EmailConfiguration $email = null;

    /**
     * Set environment configuration
     *
     * @param EnvironmentConfiguration $config
     * @return void
     */
    public function setEnvironment(EnvironmentConfiguration $config): void
    {
        $this->environment = $config;
    }

    /**
     * Get environment configuration
     *
     * @return EnvironmentConfiguration|null
     */
    public function getEnvironment(): ?EnvironmentConfiguration
    {
        return $this->environment;
    }

    /**
     * Set database configuration
     *
     * @param DatabaseConfiguration $config
     * @return void
     */
    public function setDatabase(DatabaseConfiguration $config): void
    {
        $this->database = $config;
    }

    /**
     * Get database configuration
     *
     * @return DatabaseConfiguration|null
     */
    public function getDatabase(): ?DatabaseConfiguration
    {
        return $this->database;
    }

    /**
     * Set admin configuration
     *
     * @param AdminConfiguration $config
     * @return void
     */
    public function setAdmin(AdminConfiguration $config): void
    {
        $this->admin = $config;
    }

    /**
     * Get admin configuration
     *
     * @return AdminConfiguration|null
     */
    public function getAdmin(): ?AdminConfiguration
    {
        return $this->admin;
    }

    /**
     * Set store configuration
     *
     * @param StoreConfiguration $config
     * @return void
     */
    public function setStore(StoreConfiguration $config): void
    {
        $this->store = $config;
    }

    /**
     * Get store configuration
     *
     * @return StoreConfiguration|null
     */
    public function getStore(): ?StoreConfiguration
    {
        return $this->store;
    }

    /**
     * Set backend configuration
     *
     * @param BackendConfiguration $config
     * @return void
     */
    public function setBackend(BackendConfiguration $config): void
    {
        $this->backend = $config;
    }

    /**
     * Get backend configuration
     *
     * @return BackendConfiguration|null
     */
    public function getBackend(): ?BackendConfiguration
    {
        return $this->backend;
    }

    /**
     * Set search engine configuration
     *
     * @param SearchEngineConfiguration $config
     * @return void
     */
    public function setSearchEngine(SearchEngineConfiguration $config): void
    {
        $this->searchEngine = $config;
    }

    /**
     * Get search engine configuration
     *
     * @return SearchEngineConfiguration|null
     */
    public function getSearchEngine(): ?SearchEngineConfiguration
    {
        return $this->searchEngine;
    }

    /**
     * Set Redis configuration
     *
     * @param RedisConfiguration $config
     * @return void
     */
    public function setRedis(RedisConfiguration $config): void
    {
        $this->redis = $config;
    }

    /**
     * Get Redis configuration
     *
     * @return RedisConfiguration|null
     */
    public function getRedis(): ?RedisConfiguration
    {
        return $this->redis;
    }

    /**
     * Set RabbitMQ configuration
     *
     * @param RabbitMQConfiguration $config
     * @return void
     */
    public function setRabbitMQ(RabbitMQConfiguration $config): void
    {
        $this->rabbitMQ = $config;
    }

    /**
     * Get RabbitMQ configuration
     *
     * @return RabbitMQConfiguration|null
     */
    public function getRabbitMQ(): ?RabbitMQConfiguration
    {
        return $this->rabbitMQ;
    }

    /**
     * Set logging configuration
     *
     * @param LoggingConfiguration $config
     * @return void
     */
    public function setLogging(LoggingConfiguration $config): void
    {
        $this->logging = $config;
    }

    /**
     * Get logging configuration
     *
     * @return LoggingConfiguration|null
     */
    public function getLogging(): ?LoggingConfiguration
    {
        return $this->logging;
    }

    /**
     * Set sample data configuration
     *
     * @param SampleDataConfiguration $config
     * @return void
     */
    public function setSampleData(SampleDataConfiguration $config): void
    {
        $this->sampleData = $config;
    }

    /**
     * Get sample data configuration
     *
     * @return SampleDataConfiguration|null
     */
    public function getSampleData(): ?SampleDataConfiguration
    {
        return $this->sampleData;
    }

    /**
     * Set theme configuration
     *
     * @param ThemeConfiguration $config
     * @return void
     */
    public function setTheme(ThemeConfiguration $config): void
    {
        $this->theme = $config;
    }

    /**
     * Get theme configuration
     *
     * @return ThemeConfiguration|null
     */
    public function getTheme(): ?ThemeConfiguration
    {
        return $this->theme;
    }

    /**
     * Set cron configuration
     *
     * @param CronConfiguration $config
     * @return void
     */
    public function setCron(CronConfiguration $config): void
    {
        $this->cron = $config;
    }

    /**
     * Get cron configuration
     *
     * @return CronConfiguration|null
     */
    public function getCron(): ?CronConfiguration
    {
        return $this->cron;
    }

    /**
     * Set email configuration
     *
     * @param EmailConfiguration $config
     * @return void
     */
    public function setEmail(EmailConfiguration $config): void
    {
        $this->email = $config;
    }

    /**
     * Get email configuration
     *
     * @return EmailConfiguration|null
     */
    public function getEmail(): ?EmailConfiguration
    {
        return $this->email;
    }

    /**
     * Get list of sensitive field names that should be excluded from serialization
     * These will need to be re-prompted when resuming installation
     *
     * @return array<string>
     */
    public function getSensitiveFields(): array
    {
        return [
            'database.password',
            'admin.password',
            'rabbitMQ.password',
            'email.password'
        ];
    }

    /**
     * Serialize context to array (excluding sensitive data)
     *
     * This is used to save installation configuration to file
     * so users can resume if installation fails.
     *
     * @return array<string, array<string, mixed>>
     */
    public function toArray(): array
    {
        $data = [
            '_created_at' => date('Y-m-d H:i:s')
        ];

        if ($this->environment) {
            $data['environment'] = $this->environment->toArray(false);
        }

        if ($this->database) {
            $data['database'] = $this->database->toArray(false); // Excludes password
        }

        if ($this->admin) {
            $data['admin'] = $this->admin->toArray(false); // Excludes password
        }

        if ($this->store) {
            $data['store'] = $this->store->toArray(false);
        }

        if ($this->backend) {
            $data['backend'] = $this->backend->toArray(false);
        }

        if ($this->searchEngine) {
            $data['search'] = $this->searchEngine->toArray(false);
        }

        if ($this->redis) {
            $data['redis'] = $this->redis->toArray(false);
        }

        if ($this->rabbitMQ) {
            $data['rabbitmq'] = $this->rabbitMQ->toArray(false); // Excludes password
        }

        if ($this->logging) {
            $data['logging'] = $this->logging->toArray(false);
        }

        if ($this->sampleData) {
            $data['sampleData'] = $this->sampleData->toArray(false);
        }

        if ($this->theme) {
            $data['theme'] = $this->theme->toArray(false);
        }

        // Note: Cron and Email are post-install, may not be set yet
        if ($this->cron) {
            $data['cron'] = $this->cron->toArray(false);
        }

        if ($this->email) {
            $data['email'] = $this->email->toArray(false); // Excludes password
        }

        return $data;
    }

    /**
     * Deserialize context from array
     *
     * Used when resuming installation from saved config.
     * Note: Sensitive fields (passwords) will be empty and
     * need to be re-collected.
     *
     * @param array<string, array<string, mixed>> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $context = new self();

        if (isset($data['environment'])) {
            $context->setEnvironment(EnvironmentConfiguration::fromArray($data['environment']));
        }

        if (isset($data['database'])) {
            $context->setDatabase(DatabaseConfiguration::fromArray($data['database']));
        }

        if (isset($data['admin'])) {
            $context->setAdmin(AdminConfiguration::fromArray($data['admin']));
        }

        if (isset($data['store'])) {
            $context->setStore(StoreConfiguration::fromArray($data['store']));
        }

        if (isset($data['backend'])) {
            $context->setBackend(BackendConfiguration::fromArray($data['backend']));
        }

        if (isset($data['search'])) {
            $context->setSearchEngine(SearchEngineConfiguration::fromArray($data['search']));
        }

        if (isset($data['redis'])) {
            $context->setRedis(RedisConfiguration::fromArray($data['redis']));
        }

        if (isset($data['rabbitmq'])) {
            $context->setRabbitMQ(RabbitMQConfiguration::fromArray($data['rabbitmq']));
        }

        if (isset($data['logging'])) {
            $context->setLogging(LoggingConfiguration::fromArray($data['logging']));
        }

        if (isset($data['sampleData'])) {
            $context->setSampleData(SampleDataConfiguration::fromArray($data['sampleData']));
        }

        if (isset($data['theme'])) {
            $context->setTheme(ThemeConfiguration::fromArray($data['theme']));
        }

        if (isset($data['cron'])) {
            $context->setCron(CronConfiguration::fromArray($data['cron']));
        }

        if (isset($data['email'])) {
            $context->setEmail(EmailConfiguration::fromArray($data['email']));
        }

        return $context;
    }

    /**
     * Check if context has all required configuration for installation
     *
     * These are the minimum required fields to run setup:install
     *
     * @return bool
     */
    public function isReadyForInstallation(): bool
    {
        return $this->environment !== null
            && $this->database !== null
            && $this->admin !== null
            && $this->store !== null
            && $this->backend !== null
            && $this->searchEngine !== null
            && $this->logging !== null;
    }

    /**
     * Check if any passwords are missing (need to be re-prompted)
     *
     * @return array<string> List of missing password fields
     */
    public function getMissingPasswords(): array
    {
        $missing = [];

        if ($this->database && empty($this->database->password)) {
            $missing[] = 'database.password';
        }

        if ($this->admin && empty($this->admin->password)) {
            $missing[] = 'admin.password';
        }

        if ($this->rabbitMQ && $this->rabbitMQ->enabled && empty($this->rabbitMQ->password)) {
            $missing[] = 'rabbitMQ.password';
        }

        if ($this->email && $this->email->configure && $this->email->isSmtp() && empty($this->email->password)) {
            $missing[] = 'email.password';
        }

        return $missing;
    }
}
