<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Validator;

/**
 * Validates database connection parameters
 */
class DatabaseValidator
{
    /**
     * Validate database connection
     *
     * @param string $host
     * @param string $name
     * @param string $user
     * @param string $password
     * @return array{success: bool, error: string|null}
     */
    public function validate(string $host, string $name, string $user, string $password): array
    {
        try {
            // phpcs:ignore Magento2.Functions.DiscouragedFunction
            $connection = @new \mysqli($host, $user, $password, $name);

            if ($connection->connect_error) {
                return [
                    'success' => false,
                    'error' => sprintf(
                        'Database connection failed: %s (Error %s)',
                        $connection->connect_error,
                        $connection->connect_errno
                    )
                ];
            }

            $connection->close();

            return [
                'success' => true,
                'error' => null
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Database connection failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Validate database name
     *
     * @param string $name
     * @return array{valid: bool, error: string|null}
     */
    public function validateDatabaseName(string $name): array
    {
        if (empty($name)) {
            return [
                'valid' => false,
                'error' => 'Database name cannot be empty'
            ];
        }

        // Check for valid characters (alphanumeric, underscore, hyphen)
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $name)) {
            return [
                'valid' => false,
                'error' => 'Database name can only contain letters, numbers, underscores, and hyphens'
            ];
        }

        return [
            'valid' => true,
            'error' => null
        ];
    }

    /**
     * Try to create database if it doesn't exist
     *
     * @param string $host
     * @param string $name
     * @param string $user
     * @param string $password
     * @return array{created: bool, existed: bool, error: string|null}
     */
    public function createDatabaseIfNotExists(string $host, string $name, string $user, string $password): array
    {
        try {
            // Connect without specifying database
            // phpcs:ignore Magento2.Functions.DiscouragedFunction
            $connection = @new \mysqli($host, $user, $password);

            if ($connection->connect_error) {
                return [
                    'created' => false,
                    'existed' => false,
                    'error' => sprintf('Cannot connect to MySQL server: %s', $connection->connect_error)
                ];
            }

            // Check if database exists
            $result = $connection->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '{$connection->real_escape_string($name)}'");

            if ($result && $result->num_rows > 0) {
                $connection->close();
                return [
                    'created' => false,
                    'existed' => true,
                    'error' => null
                ];
            }

            // Try to create database
            $createQuery = sprintf('CREATE DATABASE IF NOT EXISTS `%s` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci', $connection->real_escape_string($name));

            if ($connection->query($createQuery)) {
                $connection->close();
                return [
                    'created' => true,
                    'existed' => false,
                    'error' => null
                ];
            }

            $error = $connection->error;
            $connection->close();

            return [
                'created' => false,
                'existed' => false,
                'error' => sprintf('Could not create database: %s', $error)
            ];
        } catch (\Exception $e) {
            return [
                'created' => false,
                'existed' => false,
                'error' => sprintf('Database creation failed: %s', $e->getMessage())
            ];
        }
    }
}
