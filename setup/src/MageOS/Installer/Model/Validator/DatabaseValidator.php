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
            $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $host, $name);
            $pdo = new \PDO($dsn, $user, $password, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_TIMEOUT => 5,
            ]);
            $pdo = null;

            return [
                'success' => true,
                'error' => null
            ];
        } catch (\PDOException $e) {
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
            $dsn = sprintf('mysql:host=%s;charset=utf8mb4', $host);
            $pdo = new \PDO($dsn, $user, $password, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_TIMEOUT => 5,
            ]);

            // Check if database exists using prepared statement
            $stmt = $pdo->prepare(
                'SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = :name'
            );
            $stmt->execute(['name' => $name]);

            if ($stmt->fetch()) {
                return [
                    'created' => false,
                    'existed' => true,
                    'error' => null
                ];
            }

            // Database name is already validated by validateDatabaseName() to contain
            // only [a-zA-Z0-9_-], so backtick-quoting is safe here.
            // PDO prepared statements don't support parameterized identifiers.
            $pdo->exec(
                sprintf(
                    'CREATE DATABASE IF NOT EXISTS `%s` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci',
                    str_replace('`', '``', $name)
                )
            );

            return [
                'created' => true,
                'existed' => false,
                'error' => null
            ];
        } catch (\PDOException $e) {
            return [
                'created' => false,
                'existed' => false,
                'error' => sprintf('Database operation failed: %s', $e->getMessage())
            ];
        }
    }
}
