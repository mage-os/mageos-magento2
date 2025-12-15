<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Console\Command;

use MageOS\Installer\Model;
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
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * Interactive Magento installation command
 */
class InstallCommand extends Command
{
    public const NAME = 'install';

    public function __construct(
        private readonly EnvironmentConfig $environmentConfig,
        private readonly DatabaseConfig $databaseConfig,
        private readonly AdminConfig $adminConfig,
        private readonly StoreConfig $storeConfig,
        private readonly SearchEngineConfig $searchEngineConfig,
        private readonly BackendConfig $backendConfig,
        private readonly RedisConfig $redisConfig,
        private readonly RabbitMQConfig $rabbitMQConfig,
        private readonly LoggingConfig $loggingConfig,
        private readonly SampleDataConfig $sampleDataConfig,
        private readonly ThemeConfig $themeConfig,
        private readonly CronConfig $cronConfig,
        private readonly EmailConfig $emailConfig,
        private readonly DocumentRootDetector $documentRootDetector,
        private readonly EnvConfigWriter $envConfigWriter,
        private readonly ThemeInstaller $themeInstaller,
        private readonly PermissionChecker $permissionChecker,
        private readonly ConfigFileManager $configFileManager,
        private readonly PasswordValidator $passwordValidator,
        private readonly ProcessRunner $processRunner,
        private readonly CronConfigurer $cronConfigurer,
        private readonly EmailConfigurer $emailConfigurer,
        private readonly ModeConfigurer $modeConfigurer,
        private readonly ThemeConfigurer $themeConfigurer,
        private readonly IndexerConfigurer $indexerConfigurer,
        private readonly TwoFactorAuthConfigurer $twoFactorAuthConfigurer,
        ?string $name = null
    ) {
        parent::__construct($name);
    }

    /**
     * Create stage navigator with all installation stages in order
     *
     * @return Model\Stage\StageNavigator
     */
    private function createStageNavigator(): Model\Stage\StageNavigator
    {
        $stages = [
            // Welcome
            new Model\Stage\WelcomeStage(),

            // Configuration stages
            new Model\Stage\EnvironmentConfigStage($this->environmentConfig),
            new Model\Stage\DatabaseConfigStage($this->databaseConfig),
            new Model\Stage\AdminConfigStage($this->adminConfig, $this->passwordValidator),
            new Model\Stage\StoreConfigStage($this->storeConfig),
            new Model\Stage\BackendConfigStage($this->backendConfig),
            new Model\Stage\DocumentRootInfoStage($this->documentRootDetector),
            new Model\Stage\SearchEngineConfigStage($this->searchEngineConfig),
            new Model\Stage\RedisConfigStage($this->redisConfig),
            new Model\Stage\RabbitMQConfigStage($this->rabbitMQConfig),
            new Model\Stage\LoggingConfigStage($this->loggingConfig),
            new Model\Stage\SampleDataConfigStage($this->sampleDataConfig),
            new Model\Stage\ThemeConfigStage($this->themeConfig),

            // Summary and confirmation
            new Model\Stage\SummaryStage(),

            // Permission check
            new Model\Stage\PermissionCheckStage($this->permissionChecker),

            // Theme installation (before Magento)
            new Model\Stage\ThemeInstallationStage($this->themeInstaller),

            // Main Magento installation
            new Model\Stage\MagentoInstallationStage($this->getApplication()),

            // Service configuration
            new Model\Stage\ServiceConfigurationStage($this->envConfigWriter),

            // Sample data installation
            new Model\Stage\SampleDataInstallationStage($this->getApplication()),

            // Post-install configuration
            new Model\Stage\PostInstallConfigStage(
                $this->cronConfig,
                $this->emailConfig,
                $this->cronConfigurer,
                $this->emailConfigurer,
                $this->modeConfigurer,
                $this->themeConfigurer,
                $this->indexerConfigurer,
                $this->twoFactorAuthConfigurer,
                $this->processRunner
            ),

            // Completion
            new Model\Stage\CompletionStage(),
        ];

        return new Model\Stage\StageNavigator($stages);
    }

    /**
     * @inheritdoc
     */
    protected function configure(): void
    {
        $this->setName('install')
            ->setDescription('Interactive Mage-OS installation wizard')
            ->setHelp(
                'This command guides you through the Mage-OS installation process step by step.' . PHP_EOL .
                PHP_EOL .
                'Use -vvv flag to see the underlying setup:install command being executed.'
            );
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $baseDir = BP; // Magento base directory constant

            // Create installation context
            $context = new Model\InstallationContext();

            // Check for previous installation config and load into context
            if ($this->configFileManager->exists($baseDir)) {
                $savedContext = $this->handlePreviousConfig($input, $output, $baseDir);
                if ($savedContext) {
                    $context = $savedContext;
                    $output->writeln('<info>✓ Loaded previous configuration</info>');
                }
            }

            // Create stage navigator with all stages
            $navigator = $this->createStageNavigator();

            // Execute all stages with navigation support
            $success = $navigator->navigate($context, $output);

            if (!$success) {
                // Save config so user can resume
                $this->configFileManager->saveContext($baseDir, $context);
                $output->writeln('');
                $output->writeln('<comment>Installation cancelled.</comment>');
                $output->writeln('<comment>💡 Your configuration has been saved. Run "bin/magento install" to resume.</comment>');
                return Command::FAILURE;
            }

            // Save configuration before installation (for resume capability)
            // This happens during navigation, but we save again at the end
            $this->configFileManager->saveContext($baseDir, $context);

            // Delete config file on success
            $this->configFileManager->delete($baseDir);

            return Command::SUCCESS;
        } catch (\Exception $e) {
            // Actually save the config (this was missing!)
            try {
                $baseDir = BP;
                if (isset($context)) {
                    $this->configFileManager->saveContext($baseDir, $context);
                }
            } catch (\Exception $saveException) {
                // If save fails, at least tell the user
                $output->writeln('<comment>⚠️  Could not save configuration: ' . $saveException->getMessage() . '</comment>');
            }

            $output->writeln('');
            $output->writeln('<error>Installation failed: ' . $e->getMessage() . '</error>');
            $output->writeln('');
            $output->writeln('<comment>💡 Your configuration has been saved. Run "bin/magento install" to resume.</comment>');
            return Command::FAILURE;
        }
    }

    /**
     * Handle previous configuration file
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param string $baseDir
     * @return Model\InstallationContext|null
     */
    private function handlePreviousConfig(
        InputInterface $input,
        OutputInterface $output,
        string $baseDir
    ): ?Model\InstallationContext {
        $output->writeln('');
        $output->writeln('<fg=yellow>⚠️  Previous installation detected!</>');
        $output->writeln('');

        $savedContext = $this->configFileManager->loadContext($baseDir);

        if (!$savedContext) {
            $output->writeln('<comment>Configuration file exists but cannot be read. Starting fresh...</comment>');
            return null;
        }

        $output->writeln('<comment>Found saved configuration. Passwords will be re-prompted for security.</comment>');
        $output->writeln('');

        $resumeQuestion = new ConfirmationQuestion(
            '<question>? Resume previous installation?</question> [<comment>Y/n</comment>]: ',
            true
        );

        $resume = $this->getHelper('question')->ask($input, $output, $resumeQuestion);

        if (!$resume) {
            $output->writeln('<comment>Starting fresh installation...</comment>');
            $this->configFileManager->delete($baseDir);
            return null;
        }

        return $savedContext;
    }
}
