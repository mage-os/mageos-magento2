<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Config;

use MageOS\Installer\Model\Detector\DatabaseDetector;
use MageOS\Installer\Model\Validator\DatabaseValidator;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\note;
use function Laravel\Prompts\password;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\text;
use function Laravel\Prompts\warning;

/**
 * Collects database configuration with Laravel Prompts
 */
class DatabaseConfig
{
    public function __construct(
        private readonly DatabaseDetector $databaseDetector,
        private readonly DatabaseValidator $databaseValidator
    ) {
    }

    /**
     * Collect database configuration
     *
     * @return array{host: string, name: string, user: string, password: string, prefix: string}
     */
    public function collect(): array
    {
        while (true) {
            note('Database Configuration');

            // Detect database
            $detected = spin(
                message: 'Detecting MySQL/MariaDB...',
                callback: fn () => $this->databaseDetector->detect()
            );

            if ($detected) {
                info(sprintf('✓ Detected database on %s:%d', $detected['host'], $detected['port']));
                $defaultHost = $detected['host'];
            } else {
                warning('No database detected on common ports');
                $defaultHost = 'localhost';
            }

            // Database host
            $host = text(
                label: 'Database host',
                default: $defaultHost,
                placeholder: 'localhost',
                hint: 'MySQL/MariaDB hostname or IP'
            );

            // Database name
            $name = text(
                label: 'Database name',
                default: 'magento',
                placeholder: 'magento',
                hint: 'Database must exist or user must have CREATE permission',
                validate: function (string $value) {
                    $result = $this->databaseValidator->validateDatabaseName($value);
                    return $result['valid'] ? null : $result['error'];
                }
            );

            // Database user
            $user = text(
                label: 'Database user',
                default: 'root',
                placeholder: 'root',
                hint: 'User must have CREATE, ALTER, DROP permissions'
            );

            // Database password
            $pass = password(
                label: 'Database password',
                hint: 'Password for database user'
            );

            // Table prefix (optional)
            $prefix = text(
                label: 'Table prefix (optional)',
                default: '',
                placeholder: 'leave empty for no prefix',
                required: false,
                hint: 'Useful for multiple Magento installs in one database'
            );

            // Test database connection
            $validation = spin(
                message: 'Testing database connection...',
                callback: fn () => $this->databaseValidator->validate($host, $name, $user, $pass)
            );

            if ($validation['success']) {
                info('✓ Database connection successful!');
                return [
                    'host' => $host,
                    'name' => $name,
                    'user' => $user,
                    'password' => $pass,
                    'prefix' => $prefix
                ];
            }

            // Connection failed - try to create database if it doesn't exist
            warning('Database connection failed - attempting to create database...');

            $createResult = spin(
                message: 'Creating database...',
                callback: fn () => $this->databaseValidator->createDatabaseIfNotExists($host, $name, $user, $pass)
            );

            if ($createResult['created']) {
                info("✓ Database '{$name}' created successfully!");
                warning('⚠️  Database was created automatically.');
                warning('⚠️  If you are on a PRODUCTION server, verify the user has appropriate permissions!');
                note('The installation will continue with the newly created database.');

                return [
                    'host' => $host,
                    'name' => $name,
                    'user' => $user,
                    'password' => $pass,
                    'prefix' => $prefix
                ];
            }

            if ($createResult['existed']) {
                // Database existed but connection still failed - credential issue
                error('Database exists but connection failed - check credentials');
            } else {
                // Could not create database
                error('Could not create database');
                if ($createResult['error']) {
                    error($createResult['error']);
                }
            }

            // Original error for context
            error('Original error: ' . ($validation['error'] ?? 'Unknown error'));

            $retry = confirm(
                label: 'Database connection failed. Do you want to reconfigure?',
                default: true
            );

            if (!$retry) {
                throw new \RuntimeException('Database connection test failed. Installation aborted.');
            }
        }
    }
}
