<?php
/**
 * Copyright © Mage-OS, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Development server command
 *
 * Starts PHP's built-in web server for local development.
 * Inspired by Laravel's `php artisan serve`.
 *
 * @api
 */
class DevServeCommand extends Command
{
    /**
     * Default server host
     */
    private const DEFAULT_HOST = 'localhost';

    /**
     * Default server port
     */
    private const DEFAULT_PORT = 8000;

    /**
     * Maximum port to try
     */
    private const MAX_PORT = 8010;

    /**
     * @var string
     */
    private $baseDir;

    /**
     * Constructor
     *
     * @param string|null $name
     */
    public function __construct(string $name = null)
    {
        $this->baseDir = defined('BP') ? BP : getcwd();
        parent::__construct($name);
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('dev:serve')
            ->setDescription('Start development server with SQLite')
            ->addOption(
                'host',
                null,
                InputOption::VALUE_OPTIONAL,
                'Server host',
                self::DEFAULT_HOST
            )
            ->addOption(
                'port',
                'p',
                InputOption::VALUE_OPTIONAL,
                'Server port',
                self::DEFAULT_PORT
            )
            ->addOption(
                'open',
                'o',
                InputOption::VALUE_NONE,
                'Open browser after starting'
            );

        parent::configure();
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $host = $input->getOption('host');
        $port = (int)$input->getOption('port');
        $shouldOpen = $input->getOption('open');

        // Find available port
        $port = $this->findAvailablePort($host, $port, $output);

        if ($port === null) {
            $output->writeln('<error>Could not find an available port between ' .
                self::DEFAULT_PORT . ' and ' . self::MAX_PORT . '</error>');
            return Command::FAILURE;
        }

        // Display banner
        $this->displayBanner($output, $host, $port);

        // Open browser if requested
        if ($shouldOpen) {
            $this->openBrowser("http://{$host}:{$port}");
        }

        // Start server
        $this->startServer($host, $port, $output);

        return Command::SUCCESS;
    }

    /**
     * Find an available port
     *
     * @param string $host
     * @param int $startPort
     * @param OutputInterface $output
     * @return int|null
     */
    private function findAvailablePort(string $host, int $startPort, OutputInterface $output): ?int
    {
        for ($port = $startPort; $port <= self::MAX_PORT; $port++) {
            if ($this->isPortAvailable($host, $port)) {
                if ($port !== $startPort) {
                    $output->writeln("<comment>Port {$startPort} is in use, using {$port} instead.</comment>");
                }
                return $port;
            }
        }

        return null;
    }

    /**
     * Check if port is available
     *
     * @param string $host
     * @param int $port
     * @return bool
     */
    private function isPortAvailable(string $host, int $port): bool
    {
        $connection = @fsockopen($host, $port, $errno, $errstr, 1);

        if (is_resource($connection)) {
            fclose($connection);
            return false;
        }

        return true;
    }

    /**
     * Display banner with server information
     *
     * @param OutputInterface $output
     * @param string $host
     * @param int $port
     * @return void
     */
    private function displayBanner(OutputInterface $output, string $host, int $port): void
    {
        $url = "http://{$host}:{$port}";
        $adminUrl = "{$url}/admin";

        $output->writeln('');
        $output->writeln('<fg=cyan>┌─────────────────────────────────────────────────────┐</>');
        $output->writeln('<fg=cyan>│</>  <fg=green;options=bold>MAGE-OS DEVELOPMENT SERVER</>                    <fg=cyan>│</>');
        $output->writeln('<fg=cyan>└─────────────────────────────────────────────────────┘</>');
        $output->writeln('');
        $output->writeln("  <fg=green>→</> <fg=white;options=bold>Storefront:</> <fg=cyan>{$url}</>");
        $output->writeln("  <fg=green>→</> <fg=white;options=bold>Admin Panel:</> <fg=cyan>{$adminUrl}</>");
        $output->writeln("  <fg=green>→</> <fg=white;options=bold>Database:</> <fg=cyan>var/dev.sqlite</>");
        $output->writeln('');
        $output->writeln('  <fg=yellow>Press CTRL+C to stop the server</>');
        $output->writeln('');
        $output->writeln('<fg=green>Server started successfully!</>');
        $output->writeln('');
    }

    /**
     * Start the PHP built-in web server
     *
     * @param string $host
     * @param int $port
     * @param OutputInterface $output
     * @return void
     */
    private function startServer(string $host, int $port, OutputInterface $output): void
    {
        $routerScript = $this->baseDir . '/dev/router.php';
        $documentRoot = $this->baseDir . '/pub';

        if (!file_exists($routerScript)) {
            $output->writeln('<error>Router script not found: ' . $routerScript . '</error>');
            return;
        }

        // Build command
        $command = sprintf(
            'php -S %s:%d -t %s %s',
            escapeshellarg($host),
            $port,
            escapeshellarg($documentRoot),
            escapeshellarg($routerScript)
        );

        // Set environment variables
        putenv('MAGE_MODE=developer');

        // Register signal handler for graceful shutdown
        if (function_exists('pcntl_signal')) {
            declare(ticks = 1);
            pcntl_signal(SIGINT, function () use ($output) {
                $output->writeln('');
                $output->writeln('<fg=yellow>Shutting down server...</>');
                exit(0);
            });
            pcntl_signal(SIGTERM, function () use ($output) {
                $output->writeln('');
                $output->writeln('<fg=yellow>Shutting down server...</>');
                exit(0);
            });
        }

        // Execute server (blocking)
        passthru($command, $exitCode);

        if ($exitCode !== 0) {
            $output->writeln('<error>Server exited with error code: ' . $exitCode . '</error>');
        }
    }

    /**
     * Open URL in browser
     *
     * @param string $url
     * @return void
     */
    private function openBrowser(string $url): void
    {
        $os = strtoupper(substr(PHP_OS, 0, 3));

        if ($os === 'DAR') {
            // macOS
            exec("open " . escapeshellarg($url) . " > /dev/null 2>&1 &");
        } elseif ($os === 'WIN') {
            // Windows
            exec("start " . escapeshellarg($url) . " > NUL 2>&1");
        } else {
            // Linux
            exec("xdg-open " . escapeshellarg($url) . " > /dev/null 2>&1 &");
        }
    }
}
