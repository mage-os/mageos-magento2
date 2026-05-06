<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Console\Command\InstallCommand;

use MageOS\Installer\Model\Checker\PermissionChecker;
use MageOS\Installer\Model\Command\CronConfigurer;
use MageOS\Installer\Model\Command\EmailConfigurer;
use MageOS\Installer\Model\Command\IndexerConfigurer;
use MageOS\Installer\Model\Command\ModeConfigurer;
use MageOS\Installer\Model\Command\ProcessRunner;
use MageOS\Installer\Model\Command\ThemeConfigurer;
use MageOS\Installer\Model\Command\TwoFactorAuthConfigurer;
use MageOS\Installer\Model\Config\AdminConfig;
use MageOS\Installer\Model\Config\BackendConfig;
use MageOS\Installer\Model\Config\CronConfig;
use MageOS\Installer\Model\Config\DatabaseConfig;
use MageOS\Installer\Model\Config\EmailConfig;
use MageOS\Installer\Model\Config\EnvironmentConfig;
use MageOS\Installer\Model\Config\LoggingConfig;
use MageOS\Installer\Model\Config\RabbitMQConfig;
use MageOS\Installer\Model\Config\RedisConfig;
use MageOS\Installer\Model\Config\SampleDataConfig;
use MageOS\Installer\Model\Config\SearchEngineConfig;
use MageOS\Installer\Model\Config\StoreConfig;
use MageOS\Installer\Model\Config\ThemeConfig;
use MageOS\Installer\Model\Detector\DocumentRootDetector;
use MageOS\Installer\Model\Theme\ThemeInstaller;
use MageOS\Installer\Model\Validator\PasswordValidator;
use MageOS\Installer\Model\Writer\ConfigFileManager;
use MageOS\Installer\Model\Writer\EnvConfigWriter;

/**
 * Groups all InstallCommand dependencies to keep the command constructor manageable
 */
class Context
{
    /**
     * Context constructor.
     *
     * @param \MageOS\Installer\Model\Config\EnvironmentConfig $environmentConfig
     * @param \MageOS\Installer\Model\Config\DatabaseConfig $databaseConfig
     * @param \MageOS\Installer\Model\Config\AdminConfig $adminConfig
     * @param \MageOS\Installer\Model\Config\StoreConfig $storeConfig
     * @param \MageOS\Installer\Model\Config\SearchEngineConfig $searchEngineConfig
     * @param \MageOS\Installer\Model\Config\BackendConfig $backendConfig
     * @param \MageOS\Installer\Model\Config\RedisConfig $redisConfig
     * @param \MageOS\Installer\Model\Config\RabbitMQConfig $rabbitMQConfig
     * @param \MageOS\Installer\Model\Config\LoggingConfig $loggingConfig
     * @param \MageOS\Installer\Model\Config\SampleDataConfig $sampleDataConfig
     * @param \MageOS\Installer\Model\Config\ThemeConfig $themeConfig
     * @param \MageOS\Installer\Model\Config\CronConfig $cronConfig
     * @param \MageOS\Installer\Model\Config\EmailConfig $emailConfig
     * @param \MageOS\Installer\Model\Detector\DocumentRootDetector $documentRootDetector
     * @param \MageOS\Installer\Model\Writer\EnvConfigWriter $envConfigWriter
     * @param \MageOS\Installer\Model\Theme\ThemeInstaller $themeInstaller
     * @param \MageOS\Installer\Model\Checker\PermissionChecker $permissionChecker
     * @param \MageOS\Installer\Model\Writer\ConfigFileManager $configFileManager
     * @param \MageOS\Installer\Model\Validator\PasswordValidator $passwordValidator
     * @param \MageOS\Installer\Model\Command\ProcessRunner $processRunner
     * @param \MageOS\Installer\Model\Command\CronConfigurer $cronConfigurer
     * @param \MageOS\Installer\Model\Command\EmailConfigurer $emailConfigurer
     * @param \MageOS\Installer\Model\Command\ModeConfigurer $modeConfigurer
     * @param \MageOS\Installer\Model\Command\ThemeConfigurer $themeConfigurer
     * @param \MageOS\Installer\Model\Command\IndexerConfigurer $indexerConfigurer
     * @param \MageOS\Installer\Model\Command\TwoFactorAuthConfigurer $twoFactorAuthConfigurer
     * @see \MageOS\Installer\Console\Command\InstallCommand
     */
    public function __construct(
        public readonly EnvironmentConfig $environmentConfig,
        public readonly DatabaseConfig $databaseConfig,
        public readonly AdminConfig $adminConfig,
        public readonly StoreConfig $storeConfig,
        public readonly SearchEngineConfig $searchEngineConfig,
        public readonly BackendConfig $backendConfig,
        public readonly RedisConfig $redisConfig,
        public readonly RabbitMQConfig $rabbitMQConfig,
        public readonly LoggingConfig $loggingConfig,
        public readonly SampleDataConfig $sampleDataConfig,
        public readonly ThemeConfig $themeConfig,
        public readonly CronConfig $cronConfig,
        public readonly EmailConfig $emailConfig,
        public readonly DocumentRootDetector $documentRootDetector,
        public readonly EnvConfigWriter $envConfigWriter,
        public readonly ThemeInstaller $themeInstaller,
        public readonly PermissionChecker $permissionChecker,
        public readonly ConfigFileManager $configFileManager,
        public readonly PasswordValidator $passwordValidator,
        public readonly ProcessRunner $processRunner,
        public readonly CronConfigurer $cronConfigurer,
        public readonly EmailConfigurer $emailConfigurer,
        public readonly ModeConfigurer $modeConfigurer,
        public readonly ThemeConfigurer $themeConfigurer,
        public readonly IndexerConfigurer $indexerConfigurer,
        public readonly TwoFactorAuthConfigurer $twoFactorAuthConfigurer,
    ) {
    }
}
