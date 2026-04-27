<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Config;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\note;
use function Laravel\Prompts\select;

/**
 * Collects debug and logging configuration with Laravel Prompts
 */
class LoggingConfig
{
    /**
     * Collect logging configuration
     *
     * @return array{debugMode: bool, logHandler: string, logLevel: string}
     */
    public function collect(): array
    {
        note('Debug & Logging');

        // Debug mode
        $debugMode = confirm(
            label: 'Enable debug mode?',
            default: true,
            hint: 'Shows detailed errors and enables developer features'
        );

        // Log handler with descriptions
        $logHandler = select(
            label: 'Log handler',
            options: [
                'file' => 'File (var/log/system.log - recommended)',
                'syslog' => 'Syslog (system logging daemon)',
                'database' => 'Database (log table in database)'
            ],
            default: 'file',
            hint: 'Where to store application logs'
        );

        // Log level (based on debug mode)
        $defaultLevel = $debugMode ? 'debug' : 'error';

        $logLevel = select(
            label: 'Log level',
            options: [
                'debug' => 'Debug (most verbose - development)',
                'info' => 'Info (informational messages)',
                'notice' => 'Notice (normal but significant)',
                'warning' => 'Warning (potential issues)',
                'error' => 'Error (runtime errors - production)',
                'critical' => 'Critical (critical conditions)',
                'alert' => 'Alert (action required immediately)',
                'emergency' => 'Emergency (system unusable)'
            ],
            default: $defaultLevel,
            scroll: 8,
            hint: 'Minimum severity level to log'
        );

        return [
            'debugMode' => $debugMode,
            'logHandler' => $logHandler,
            'logLevel' => $logLevel
        ];
    }
}
