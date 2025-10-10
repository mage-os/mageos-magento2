<?php
/**
 * Copyright © Mage-OS, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Model;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Helper\QuestionHelper;

/**
 * Auto-installer for development mode with SQLite
 *
 * Provides one-command installation experience similar to Laravel.
 * Creates SQLite database and installs Magento with sensible defaults.
 *
 * @api
 */
class AutoInstaller
{
    /**
     * Default admin credentials for development
     */
    private const DEFAULT_ADMIN_USER = 'admin';
    private const DEFAULT_ADMIN_PASS = 'admin123';
    private const DEFAULT_ADMIN_EMAIL = 'admin@example.com';
    private const DEFAULT_ADMIN_FIRSTNAME = 'Dev';
    private const DEFAULT_ADMIN_LASTNAME = 'Admin';

    /**
     * @var string
     */
    private $baseDir;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->baseDir = defined('BP') ? BP : getcwd();
    }

    /**
     * Check if Magento needs installation
     *
     * @return bool
     */
    public function needsInstall(): bool
    {
        $dbPath = $this->baseDir . '/var/dev.sqlite';

        // If database file doesn't exist, needs install
        if (!file_exists($dbPath)) {
            return true;
        }

        // If file exists but is empty or invalid, needs install
        try {
            $pdo = new \PDO('sqlite:' . $dbPath);
            $result = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='admin_user'");
            $table = $result ? $result->fetch() : false;

            // If admin_user table doesn't exist, needs install
            return $table === false;
        } catch (\Exception $e) {
            // Database is corrupted, needs reinstall
            return true;
        }
    }

    /**
     * Check if system meets requirements
     *
     * @param OutputInterface $output
     * @return bool
     */
    public function checkRequirements(OutputInterface $output): bool
    {
        $required = [
            'pdo_sqlite' => 'SQLite PDO extension',
            'intl' => 'Internationalization extension',
            'gd' => 'GD image library',
            'zip' => 'Zip extension',
            'curl' => 'cURL extension',
        ];

        $missing = [];
        foreach ($required as $ext => $name) {
            if (!extension_loaded($ext)) {
                $missing[] = $name . " ($ext)";
            }
        }

        if (!empty($missing)) {
            $output->writeln('<error>Missing required PHP extensions:</error>');
            foreach ($missing as $ext) {
                $output->writeln("  - {$ext}");
            }
            $output->writeln('');
            $output->writeln('Install missing extensions and try again.');
            return false;
        }

        // Check if vendor directory exists
        if (!is_dir($this->baseDir . '/vendor')) {
            $output->writeln('<error>Vendor directory not found. Please run:</error>');
            $output->writeln('  composer install');
            return false;
        }

        return true;
    }

    /**
     * Prompt user for installation
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param QuestionHelper $helper
     * @return bool
     */
    public function promptForInstall(
        InputInterface $input,
        OutputInterface $output,
        QuestionHelper $helper
    ): bool {
        $output->writeln('');
        $output->writeln('<fg=yellow>No Magento installation found.</>');
        $output->writeln('');

        $question = new ConfirmationQuestion(
            'Would you like to install Magento now with SQLite? [Y/n] ',
            true
        );

        return $helper->ask($input, $output, $question);
    }

    /**
     * Collect installation credentials from user
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param QuestionHelper $helper
     * @return array
     */
    public function collectCredentials(
        InputInterface $input,
        OutputInterface $output,
        QuestionHelper $helper
    ): array {
        $output->writeln('');
        $output->writeln('<info>Please provide admin credentials (press Enter for defaults):</info>');
        $output->writeln('');

        // Admin username
        $question = new Question(
            sprintf('Admin username [<comment>%s</comment>]: ', self::DEFAULT_ADMIN_USER),
            self::DEFAULT_ADMIN_USER
        );
        $adminUser = $helper->ask($input, $output, $question);

        // Admin password
        $question = new Question(
            sprintf('Admin password [<comment>%s</comment>]: ', self::DEFAULT_ADMIN_PASS),
            self::DEFAULT_ADMIN_PASS
        );
        $adminPassword = $helper->ask($input, $output, $question);

        // Admin email
        $question = new Question(
            sprintf('Admin email [<comment>%s</comment>]: ', self::DEFAULT_ADMIN_EMAIL),
            self::DEFAULT_ADMIN_EMAIL
        );
        $adminEmail = $helper->ask($input, $output, $question);

        return [
            'admin_user' => $adminUser,
            'admin_password' => $adminPassword,
            'admin_email' => $adminEmail,
            'admin_firstname' => self::DEFAULT_ADMIN_FIRSTNAME,
            'admin_lastname' => self::DEFAULT_ADMIN_LASTNAME,
        ];
    }

    /**
     * Run Magento installation
     *
     * @param array $credentials
     * @param string $baseUrl
     * @param OutputInterface $output
     * @return bool
     */
    public function install(array $credentials, string $baseUrl, OutputInterface $output): bool
    {
        $output->writeln('');
        $output->writeln('<info>Installing Magento with SQLite...</info>');
        $output->writeln('');

        // Build setup:install command
        $command = $this->buildInstallCommand($credentials, $baseUrl);

        // Show what we're doing
        $output->writeln('<comment>This may take 1-2 minutes...</comment>');
        $output->writeln('');

        // Execute installation
        $exitCode = 0;
        passthru($command, $exitCode);

        if ($exitCode !== 0) {
            $output->writeln('');
            $output->writeln('<error>Installation failed. Check var/log/install.log for details.</error>');

            // Clean up corrupted database
            $dbPath = $this->baseDir . '/var/dev.sqlite';
            if (file_exists($dbPath)) {
                @unlink($dbPath);
            }

            return false;
        }

        $output->writeln('');
        $output->writeln('<info>Optimizing for development...</info>');
        $this->optimize($output);

        $output->writeln('');
        $output->writeln('<fg=green>✓ Installation complete!</>');
        $output->writeln('');

        return true;
    }

    /**
     * Build setup:install command
     *
     * @param array $credentials
     * @param string $baseUrl
     * @return string
     */
    private function buildInstallCommand(array $credentials, string $baseUrl): string
    {
        $phpBinary = PHP_BINARY;
        $magentoScript = $this->baseDir . '/bin/magento';

        $params = [
            'setup:install',
            '--db-host=""',
            '--db-name="var/dev.sqlite"',
            '--db-user=""',
            '--db-password=""',
            '--base-url="' . $baseUrl . '"',
            '--backend-frontname=admin',
            '--admin-user="' . $credentials['admin_user'] . '"',
            '--admin-password="' . $credentials['admin_password'] . '"',
            '--admin-email="' . $credentials['admin_email'] . '"',
            '--admin-firstname="' . $credentials['admin_firstname'] . '"',
            '--admin-lastname="' . $credentials['admin_lastname'] . '"',
            '--language=en_US',
            '--currency=USD',
            '--timezone=UTC',
            '--use-rewrites=1',
            '--session-save=files',
            '--cleanup-database',
        ];

        return escapeshellcmd($phpBinary) . ' ' . escapeshellarg($magentoScript) . ' ' . implode(' ', $params);
    }

    /**
     * Optimize Magento for development mode
     *
     * @param OutputInterface $output
     * @return void
     */
    public function optimize(OutputInterface $output): void
    {
        $optimizations = [
            'deploy:mode:set developer' => 'Setting developer mode',
            'cache:disable full_page' => 'Disabling full page cache',
            'cache:disable block_html' => 'Disabling block HTML cache',
            'indexer:set-mode schedule' => 'Setting indexers to schedule mode',
        ];

        foreach ($optimizations as $command => $description) {
            $output->writeln("  → {$description}...");
            $this->runMagentoCommand($command);
        }
    }

    /**
     * Run a Magento CLI command
     *
     * @param string $command
     * @return void
     */
    private function runMagentoCommand(string $command): void
    {
        $phpBinary = PHP_BINARY;
        $magentoScript = $this->baseDir . '/bin/magento';

        $fullCommand = escapeshellcmd($phpBinary) . ' ' .
                       escapeshellarg($magentoScript) . ' ' .
                       $command . ' 2>&1 > /dev/null';

        exec($fullCommand);
    }

    /**
     * Prepare environment files for SQLite
     *
     * Creates app/etc/env.php with SQLite configuration if it doesn't exist
     *
     * @return void
     */
    public function prepareEnvironment(): void
    {
        $envPath = $this->baseDir . '/app/etc/env.php';
        $configPath = $this->baseDir . '/app/etc/config.php';

        // Create config.php if doesn't exist
        if (!file_exists($configPath)) {
            $configContent = <<<'PHP'
<?php
return [
    'modules' => []
];
PHP;
            @file_put_contents($configPath, $configContent);
        }

        // Create env.php with SQLite config if doesn't exist
        if (!file_exists($envPath)) {
            $envContent = <<<'PHP'
<?php
return [
    'db' => [
        'table_prefix' => '',
        'connection' => [
            'default' => [
                'host' => '',
                'dbname' => 'var/dev.sqlite',
                'username' => '',
                'password' => '',
                'model' => 'mysql4',
                'engine' => 'innodb',
                'initStatements' => '',
                'active' => '1',
                'driver_options' => [
                    'sqlite_query_logging' => true
                ]
            ]
        ]
    ],
    'backend' => [
        'frontName' => 'admin'
    ],
    'crypt' => [
        'key' => ''
    ],
    'session' => [
        'save' => 'files'
    ],
    'cache' => [
        'frontend' => [
            'default' => [
                'backend' => 'Magento\\Framework\\Cache\\Backend\\File'
            ],
            'page_cache' => [
                'backend' => 'Magento\\Framework\\Cache\\Backend\\File'
            ]
        ]
    ],
    'install' => [
        'date' => 'Wed, 10 Oct 2025 00:00:00 +0000'
    ]
];
PHP;
            $etcDir = dirname($envPath);
            if (!is_dir($etcDir)) {
                mkdir($etcDir, 0770, true);
            }
            @file_put_contents($envPath, $envContent);
        }
    }
}
