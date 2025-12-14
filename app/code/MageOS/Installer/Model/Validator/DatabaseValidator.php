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
}
