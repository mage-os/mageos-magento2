<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Console\Command;

use MageOS\Installer\Console\Command\InstallCommand\Context;
use MageOS\Installer\Model;
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

    /**
     * @param Context $context
     * @param string|null $name
     */
    public function __construct(
        private readonly Context $context,
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
            new Model\Stage\EnvironmentConfigStage($this->context->environmentConfig),
            new Model\Stage\DatabaseConfigStage($this->context->databaseConfig),
            new Model\Stage\AdminConfigStage($this->context->adminConfig, $this->context->passwordValidator),
            new Model\Stage\StoreConfigStage($this->context->storeConfig),
            new Model\Stage\BackendConfigStage($this->context->backendConfig),
            new Model\Stage\DocumentRootInfoStage($this->context->documentRootDetector),
            new Model\Stage\SearchEngineConfigStage($this->context->searchEngineConfig),
            new Model\Stage\RedisConfigStage($this->context->redisConfig),
            new Model\Stage\RabbitMQConfigStage($this->context->rabbitMQConfig),
            new Model\Stage\LoggingConfigStage($this->context->loggingConfig),
            new Model\Stage\SampleDataConfigStage($this->context->sampleDataConfig),
            new Model\Stage\ThemeConfigStage($this->context->themeConfig),

            // Summary and confirmation
            new Model\Stage\SummaryStage(),

            // Permission check
            new Model\Stage\PermissionCheckStage($this->context->permissionChecker),

            // Theme installation (before Magento)
            new Model\Stage\ThemeInstallationStage($this->context->themeInstaller),

            // Main Magento installation
            new Model\Stage\MagentoInstallationStage($this->getApplication()),

            // Service configuration
            new Model\Stage\ServiceConfigurationStage($this->context->envConfigWriter),

            // Sample data installation
            new Model\Stage\SampleDataInstallationStage($this->getApplication()),

            // Post-install configuration
            new Model\Stage\PostInstallConfigStage(
                $this->context->cronConfig,
                $this->context->emailConfig,
                $this->context->cronConfigurer,
                $this->context->emailConfigurer,
                $this->context->modeConfigurer,
                $this->context->themeConfigurer,
                $this->context->indexerConfigurer,
                $this->context->twoFactorAuthConfigurer,
                $this->context->processRunner
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
        if (!function_exists('Laravel\\Prompts\\confirm')) {
            $output->writeln('');
            $output->writeln(
                '<error>The interactive installer requires the "laravel/prompts" package, '
                . 'which is not installed.</error>'
            );
            $output->writeln('');
            $output->writeln(
                '<comment>Run the following from your project root to install it:</comment>'
            );
            $output->writeln('  <info>composer require --dev laravel/prompts</info>');
            $output->writeln('');
            return Command::FAILURE;
        }

        try {
            $baseDir = BP; // Magento base directory constant

            // Create installation context
            $context = new Model\InstallationContext();

            // Check for previous installation config and load into context
            if ($this->context->configFileManager->exists($baseDir)) {
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
                $this->context->configFileManager->saveContext($baseDir, $context);
                $output->writeln('');
                $output->writeln('<comment>Installation cancelled.</comment>');
                $resumeHint = 'Your configuration has been saved.'
                    . ' Run "bin/magento install" to resume.';
                $output->writeln('<comment>' . $resumeHint . '</comment>');
                return Command::FAILURE;
            }

            // Save configuration before installation (for resume capability)
            // This happens during navigation, but we save again at the end
            $this->context->configFileManager->saveContext($baseDir, $context);

            // Delete config file on success
            $this->context->configFileManager->delete($baseDir);

            return Command::SUCCESS;
        } catch (\Exception $e) {
            // Actually save the config (this was missing!)
            try {
                $baseDir = BP;
                if (isset($context)) {
                    $this->context->configFileManager->saveContext($baseDir, $context);
                }
            } catch (\Exception $saveException) {
                $output->writeln(
                    '<comment>Could not save configuration: '
                    . $saveException->getMessage() . '</comment>'
                );
            }

            $output->writeln('');
            $output->writeln(
                '<error>Installation failed: '
                . $e->getMessage() . '</error>'
            );
            $output->writeln('');
            $resumeHint = 'Your configuration has been saved.'
                . ' Run "bin/magento install" to resume.';
            $output->writeln('<comment>' . $resumeHint . '</comment>');
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

        $savedContext = $this->context->configFileManager->loadContext($baseDir);

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
            $this->context->configFileManager->delete($baseDir);
            return null;
        }

        return $savedContext;
    }
}
