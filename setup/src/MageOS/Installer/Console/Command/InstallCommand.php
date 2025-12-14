<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Console\Command;

use MageOS\Installer\Model\Config\AdminConfig;
use MageOS\Installer\Model\Config\BackendConfig;
use MageOS\Installer\Model\Config\DatabaseConfig;
use MageOS\Installer\Model\Config\LoggingConfig;
use MageOS\Installer\Model\Config\RabbitMQConfig;
use MageOS\Installer\Model\Config\RedisConfig;
use MageOS\Installer\Model\Config\SampleDataConfig;
use MageOS\Installer\Model\Config\SearchEngineConfig;
use MageOS\Installer\Model\Config\StoreConfig;
use MageOS\Installer\Model\Config\ThemeConfig;
use MageOS\Installer\Model\Detector\DocumentRootDetector;
use MageOS\Installer\Model\Theme\ThemeInstaller;
use MageOS\Installer\Model\Writer\EnvConfigWriter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
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
        private readonly DocumentRootDetector $documentRootDetector,
        private readonly EnvConfigWriter $envConfigWriter,
        private readonly ThemeInstaller $themeInstaller,
        ?string $name = null
    ) {
        parent::__construct($name);
    }

    /**
     * @inheritdoc
     */
    protected function configure(): void
    {
        $this->setName('install')
            ->setDescription('Interactive Mage-OS installation wizard')
            ->setHelp('This command guides you through the Mage-OS installation process step by step.');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->displayWelcome($output);

            $baseDir = BP; // Magento base directory constant

            // Collect Stage 1 configuration (Core + Basic Services)
            $dbConfig = $this->databaseConfig->collect($input, $output, $this->getHelper('question'));
            $adminConfig = $this->adminConfig->collect($input, $output, $this->getHelper('question'));
            $storeConfig = $this->storeConfig->collect($input, $output, $this->getHelper('question'), $baseDir);
            $backendConfig = $this->backendConfig->collect($input, $output, $this->getHelper('question'));

            // Document root detection
            $this->displayDocumentRootInfo($output, $baseDir);

            $searchConfig = $this->searchEngineConfig->collect($input, $output, $this->getHelper('question'));

            // Collect Stage 2 configuration (Redis, RabbitMQ, Logging, Sample Data)
            $redisConfig = $this->redisConfig->collect($input, $output, $this->getHelper('question'));
            $rabbitMqConfig = $this->rabbitMQConfig->collect($input, $output, $this->getHelper('question'));
            $loggingConfig = $this->loggingConfig->collect($input, $output, $this->getHelper('question'));
            $sampleDataConfig = $this->sampleDataConfig->collect($input, $output, $this->getHelper('question'));

            // Collect Stage 3 configuration (Theme)
            $themeConfig = $this->themeConfig->collect($input, $output, $this->getHelper('question'));

            // Show configuration summary
            $this->displaySummary(
                $output,
                $dbConfig,
                $adminConfig,
                $storeConfig,
                $backendConfig,
                $searchConfig,
                $redisConfig,
                $rabbitMqConfig,
                $loggingConfig,
                $sampleDataConfig,
                $themeConfig
            );

            // Confirm installation
            if (!$this->confirmInstallation($input, $output)) {
                $output->writeln('<comment>Installation cancelled.</comment>');
                return Command::FAILURE;
            }

            // Run installation
            $this->runInstallation(
                $input,
                $output,
                $dbConfig,
                $adminConfig,
                $storeConfig,
                $backendConfig,
                $searchConfig,
                $redisConfig,
                $rabbitMqConfig,
                $loggingConfig,
                $sampleDataConfig,
                $themeConfig,
                $baseDir
            );

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('');
            $output->writeln('<error>Installation failed: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }

    /**
     * Display welcome message
     *
     * @param OutputInterface $output
     * @return void
     */
    private function displayWelcome(OutputInterface $output): void
    {
        $output->writeln('');
        $output->writeln('<fg=cyan>🚀 Welcome to Mage-OS Interactive Installer!</>');
        $output->writeln('');
        $output->writeln('Let\'s set up your store step by step.');
    }

    /**
     * Display document root information
     *
     * @param OutputInterface $output
     * @param string $baseDir
     * @return void
     */
    private function displayDocumentRootInfo(OutputInterface $output, string $baseDir): void
    {
        $output->writeln('');
        $output->writeln('<info>=== Document Root ===</info>');

        $detection = $this->documentRootDetector->detect($baseDir);

        if ($detection['isPub']) {
            $output->writeln('<info>ℹ️  Detected: Document root is /pub</info>');
            $output->writeln('<info>✓ Using secure document root configuration</info>');
        } else {
            $output->writeln('<comment>ℹ️  Detected: Document root is project root</comment>');
            $output->writeln('<comment>' . $detection['recommendation'] . '</comment>');
        }
    }

    /**
     * Display configuration summary
     *
     * @param OutputInterface $output
     * @param array<string, mixed> $dbConfig
     * @param array<string, mixed> $adminConfig
     * @param array<string, mixed> $storeConfig
     * @param array<string, mixed> $backendConfig
     * @param array<string, mixed> $searchConfig
     * @param array<string, mixed> $redisConfig
     * @param array<string, mixed>|null $rabbitMqConfig
     * @param array<string, mixed> $loggingConfig
     * @param array<string, mixed> $sampleDataConfig
     * @param array<string, mixed> $themeConfig
     * @return void
     */
    private function displaySummary(
        OutputInterface $output,
        array $dbConfig,
        array $adminConfig,
        array $storeConfig,
        array $backendConfig,
        array $searchConfig,
        array $redisConfig,
        ?array $rabbitMqConfig,
        array $loggingConfig,
        array $sampleDataConfig,
        array $themeConfig
    ): void {
        $output->writeln('');
        $output->writeln('<fg=cyan>🎯 Configuration complete! Here\'s what will be installed:</>');
        $output->writeln('');
        $output->writeln(sprintf('  <info>Database:</info> %s@%s/%s',
            $dbConfig['user'],
            $dbConfig['host'],
            $dbConfig['name']
        ));
        $output->writeln(sprintf('  <info>Admin:</info> %s', $adminConfig['email']));
        $output->writeln(sprintf('  <info>Store:</info> %s', $storeConfig['baseUrl']));
        $output->writeln(sprintf('  <info>Backend Path:</info> %s', $backendConfig['frontname']));
        $output->writeln(sprintf('  <info>Search Engine:</info> %s (%s:%d)',
            $searchConfig['engine'],
            $searchConfig['host'],
            $searchConfig['port']
        ));

        // Redis configuration
        if ($redisConfig['session'] || $redisConfig['cache'] || $redisConfig['fpc']) {
            $redisFeatures = [];
            if ($redisConfig['session']) {
                $redisFeatures[] = 'Sessions';
            }
            if ($redisConfig['cache']) {
                $redisFeatures[] = 'Cache';
            }
            if ($redisConfig['fpc']) {
                $redisFeatures[] = 'FPC';
            }
            $output->writeln(sprintf('  <info>Redis:</info> %s', implode(', ', $redisFeatures)));
        }

        // RabbitMQ configuration
        if ($rabbitMqConfig && $rabbitMqConfig['enabled']) {
            $output->writeln(sprintf('  <info>RabbitMQ:</info> %s:%d',
                $rabbitMqConfig['host'],
                $rabbitMqConfig['port']
            ));
        }

        // Debug and logging
        $output->writeln(sprintf('  <info>Debug Mode:</info> %s', $loggingConfig['debugMode'] ? 'ON' : 'OFF'));
        $output->writeln(sprintf('  <info>Log Level:</info> %s', $loggingConfig['logLevel']));

        // Sample data
        if ($sampleDataConfig['install']) {
            $output->writeln('  <info>Sample Data:</info> Yes');
        }

        // Theme
        if ($themeConfig['install'] && $themeConfig['theme']) {
            $themeName = match($themeConfig['theme']) {
                'hyva' => 'Hyva',
                'luma' => 'Luma',
                'blank' => 'Blank',
                default => $themeConfig['theme']
            };
            $output->writeln(sprintf('  <info>Theme:</info> %s', $themeName));
        }

        $output->writeln('');
    }

    /**
     * Confirm installation with user
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool
     */
    private function confirmInstallation(InputInterface $input, OutputInterface $output): bool
    {
        $question = new ConfirmationQuestion(
            '? <question>Proceed with installation?</question> [<comment>Y/n</comment>]: ',
            true
        );

        return (bool)$this->getHelper('question')->ask($input, $output, $question);
    }

    /**
     * Run the actual installation
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param array<string, mixed> $dbConfig
     * @param array<string, mixed> $adminConfig
     * @param array<string, mixed> $storeConfig
     * @param array<string, mixed> $backendConfig
     * @param array<string, mixed> $searchConfig
     * @param array<string, mixed> $redisConfig
     * @param array<string, mixed>|null $rabbitMqConfig
     * @param array<string, mixed> $loggingConfig
     * @param array<string, mixed> $sampleDataConfig
     * @param array<string, mixed> $themeConfig
     * @param string $baseDir
     * @return void
     * @throws \Exception
     */
    private function runInstallation(
        InputInterface $input,
        OutputInterface $output,
        array $dbConfig,
        array $adminConfig,
        array $storeConfig,
        array $backendConfig,
        array $searchConfig,
        array $redisConfig,
        ?array $rabbitMqConfig,
        array $loggingConfig,
        array $sampleDataConfig,
        array $themeConfig,
        string $baseDir
    ): void {
        $output->writeln('');
        $output->writeln('<fg=cyan>🚀 Starting installation...</>');
        $output->writeln('');

        // Build setup:install arguments
        $arguments = [
            'command' => 'setup:install',
            '--db-host' => $dbConfig['host'],
            '--db-name' => $dbConfig['name'],
            '--db-user' => $dbConfig['user'],
            '--db-password' => $dbConfig['password'],
            '--admin-firstname' => $adminConfig['firstName'],
            '--admin-lastname' => $adminConfig['lastName'],
            '--admin-email' => $adminConfig['email'],
            '--admin-user' => $adminConfig['username'],
            '--admin-password' => $adminConfig['password'],
            '--base-url' => $storeConfig['baseUrl'],
            '--backend-frontname' => $backendConfig['frontname'],
            '--language' => $storeConfig['language'],
            '--currency' => $storeConfig['currency'],
            '--timezone' => $storeConfig['timezone'],
            '--use-rewrites' => $storeConfig['useRewrites'] ? '1' : '0',
            '--search-engine' => $searchConfig['engine'],
            '--elasticsearch-host' => sprintf('%s:%d', $searchConfig['host'], $searchConfig['port']),
            '--cleanup-database' => true
        ];

        // Add optional parameters
        if (!empty($dbConfig['prefix'])) {
            $arguments['--db-prefix'] = $dbConfig['prefix'];
        }

        if (!empty($searchConfig['prefix'])) {
            $arguments['--elasticsearch-index-prefix'] = $searchConfig['prefix'];
        }

        // Get setup:install command
        $setupInstallCommand = $this->getApplication()->find('setup:install');

        // Create input for setup:install
        $setupInput = new ArrayInput($arguments);
        $setupInput->setInteractive(false);

        // Run setup:install
        $output->writeln('<comment>🔄 Installing Magento core...</comment>');
        $returnCode = $setupInstallCommand->run($setupInput, $output);

        if ($returnCode !== 0) {
            throw new \RuntimeException('Installation failed. Please check the errors above.');
        }

        // Configure additional services
        $this->configureServices($output, $redisConfig, $rabbitMqConfig);

        // Install sample data if requested
        if ($sampleDataConfig['install']) {
            $this->installSampleData($output);
        }

        // Install theme if requested
        if ($themeConfig['install']) {
            $this->themeInstaller->install($baseDir, $themeConfig, $output);
        }

        $this->displaySuccess($output, $storeConfig, $backendConfig, $adminConfig, $loggingConfig, $sampleDataConfig, $themeConfig);
    }

    /**
     * Configure additional services (Redis, RabbitMQ)
     *
     * @param OutputInterface $output
     * @param array<string, mixed> $redisConfig
     * @param array<string, mixed>|null $rabbitMqConfig
     * @return void
     */
    private function configureServices(
        OutputInterface $output,
        array $redisConfig,
        ?array $rabbitMqConfig
    ): void {
        if ($redisConfig['session'] || $redisConfig['cache'] || $redisConfig['fpc']) {
            $output->writeln('');
            $output->writeln('<comment>🔄 Configuring Redis...</comment>');
            try {
                $this->envConfigWriter->writeRedisConfig($redisConfig);
                $output->writeln('<info>✓ Redis configured</info>');
            } catch (\Exception $e) {
                $output->writeln('<error>❌ Redis configuration failed: ' . $e->getMessage() . '</error>');
            }
        }

        if ($rabbitMqConfig && $rabbitMqConfig['enabled']) {
            $output->writeln('');
            $output->writeln('<comment>🔄 Configuring RabbitMQ...</comment>');
            try {
                $this->envConfigWriter->writeRabbitMQConfig($rabbitMqConfig);
                $output->writeln('<info>✓ RabbitMQ configured</info>');
            } catch (\Exception $e) {
                $output->writeln('<error>❌ RabbitMQ configuration failed: ' . $e->getMessage() . '</error>');
            }
        }
    }

    /**
     * Install sample data
     *
     * @param OutputInterface $output
     * @return void
     */
    private function installSampleData(OutputInterface $output): void
    {
        $output->writeln('');
        $output->writeln('<comment>🔄 Installing sample data...</comment>');

        try {
            // Deploy sample data
            $sampleDataCommand = $this->getApplication()->find('sampledata:deploy');
            $sampleDataInput = new ArrayInput(['command' => 'sampledata:deploy']);
            $sampleDataInput->setInteractive(false);
            $sampleDataCommand->run($sampleDataInput, $output);

            // Run setup:upgrade to install sample data modules
            $upgradeCommand = $this->getApplication()->find('setup:upgrade');
            $upgradeInput = new ArrayInput(['command' => 'setup:upgrade']);
            $upgradeInput->setInteractive(false);
            $upgradeCommand->run($upgradeInput, $output);

            $output->writeln('<info>✓ Sample data installed</info>');
        } catch (\Exception $e) {
            $output->writeln('<comment>⚠️  Sample data installation failed: ' . $e->getMessage() . '</comment>');
            $output->writeln('<comment>   You can install it later with: bin/magento sampledata:deploy</comment>');
        }
    }

    /**
     * Display success message
     *
     * @param OutputInterface $output
     * @param array<string, mixed> $storeConfig
     * @param array<string, mixed> $backendConfig
     * @param array<string, mixed> $adminConfig
     * @param array<string, mixed> $loggingConfig
     * @param array<string, mixed> $sampleDataConfig
     * @param array<string, mixed> $themeConfig
     * @return void
     */
    private function displaySuccess(
        OutputInterface $output,
        array $storeConfig,
        array $backendConfig,
        array $adminConfig,
        array $loggingConfig,
        array $sampleDataConfig,
        array $themeConfig
    ): void {
        $adminUrl = rtrim($storeConfig['baseUrl'], '/') . '/' . $backendConfig['frontname'];

        $output->writeln('');
        $output->writeln('<fg=green>🎉 Mage-OS Installation Complete!</>');
        $output->writeln('');
        $output->writeln('<info>=== Access Information ===</info>');
        $output->writeln('');
        $output->writeln(sprintf('  <info>🌐 Storefront:</info> %s', $storeConfig['baseUrl']));
        $output->writeln(sprintf('  <info>🔐 Admin Panel:</info> %s', $adminUrl));
        $output->writeln(sprintf('  <info>👤 Admin Username:</info> %s', $adminConfig['username']));
        $output->writeln(sprintf('  <info>📧 Admin Email:</info> %s', $adminConfig['email']));
        $output->writeln('');
        $output->writeln('<info>=== Next Steps ===</info>');
        $output->writeln('');
        $output->writeln('  1. Clear cache:');
        $output->writeln('     <comment>bin/magento cache:clean</comment>');
        $output->writeln('');

        if ($loggingConfig['debugMode']) {
            $output->writeln('  2. For production, disable debug mode:');
            $output->writeln('     <comment>bin/magento deploy:mode:set production</comment>');
            $output->writeln('');
            $output->writeln('  3. Open your store:');
        } else {
            $output->writeln('  2. Open your store:');
        }
        $output->writeln('     <comment>' . $storeConfig['baseUrl'] . '</comment>');
        $output->writeln('');

        if ($sampleDataConfig['install']) {
            $output->writeln('  <comment>ℹ️  Sample data has been installed for development/testing purposes</comment>');
            $output->writeln('');
        }

        if ($themeConfig['install'] && $themeConfig['theme']) {
            $themeName = match($themeConfig['theme']) {
                'hyva' => 'Hyva',
                'luma' => 'Luma',
                'blank' => 'Blank',
                default => $themeConfig['theme']
            };
            $output->writeln(sprintf('  <comment>ℹ️  %s theme has been installed</comment>', $themeName));
            $output->writeln('');
        }

        $output->writeln('<fg=cyan>Happy coding! 🚀</>');
        $output->writeln('');
    }
}
