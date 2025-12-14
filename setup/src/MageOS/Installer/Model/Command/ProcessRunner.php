<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Command;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * Safe process execution wrapper
 *
 * Replaces dangerous exec() calls with Symfony Process component
 */
class ProcessRunner
{
    /**
     * Run a command safely using Symfony Process
     *
     * @param array<string> $command Command and arguments as array
     * @param string $cwd Working directory
     * @param int $timeout Timeout in seconds (default 5 minutes)
     * @return ProcessResult
     */
    public function run(array $command, string $cwd, int $timeout = 300): ProcessResult
    {
        $process = new Process($command, $cwd, null, null, $timeout);

        try {
            $process->mustRun();

            return new ProcessResult(
                true,
                $process->getOutput(),
                $process->getErrorOutput()
            );
        } catch (ProcessFailedException $e) {
            return new ProcessResult(
                false,
                $process->getOutput(),
                $process->getErrorOutput() ?: $e->getMessage()
            );
        }
    }

    /**
     * Run a Magento CLI command
     *
     * @param string $magentoCommand Command after "bin/magento" (e.g., "cron:install")
     * @param string $baseDir Magento base directory
     * @param int $timeout Timeout in seconds
     * @return ProcessResult
     */
    public function runMagentoCommand(string $magentoCommand, string $baseDir, int $timeout = 300): ProcessResult
    {
        // Split command into parts
        $parts = explode(' ', $magentoCommand);

        // Build command array
        $command = array_merge(['bin/magento'], $parts);

        return $this->run($command, $baseDir, $timeout);
    }
}
