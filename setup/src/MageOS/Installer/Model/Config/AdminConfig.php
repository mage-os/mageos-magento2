<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Config;

use MageOS\Installer\Model\Validator\EmailValidator;

use function Laravel\Prompts\info;
use function Laravel\Prompts\note;
use function Laravel\Prompts\password;
use function Laravel\Prompts\text;
use function Laravel\Prompts\warning;

/**
 * Collects admin account configuration with Laravel Prompts
 */
class AdminConfig
{
    public function __construct(
        private readonly EmailValidator $emailValidator
    ) {
    }

    /**
     * Collect admin account configuration
     *
     * @return array{firstName: string, lastName: string, email: string, username: string, password: string}
     */
    public function collect(): array
    {
        note('Admin Account');

        // First name
        $firstName = text(
            label: 'Admin first name',
            placeholder: 'John',
            hint: 'First name of the admin user',
            validate: fn (string $value) => empty($value) ? 'First name cannot be empty' : null
        );

        // Last name
        $lastName = text(
            label: 'Admin last name',
            placeholder: 'Doe',
            hint: 'Last name of the admin user',
            validate: fn (string $value) => empty($value) ? 'Last name cannot be empty' : null
        );

        // Email
        $email = text(
            label: 'Admin email',
            placeholder: 'admin@example.com',
            hint: 'Email address for admin account',
            validate: function (string $value) {
                $result = $this->emailValidator->validate($value);
                return $result['valid'] ? null : $result['error'];
            }
        );

        // Username (no default for security!)
        $username = text(
            label: 'Admin username',
            placeholder: 'myadmin',
            hint: 'Username to login to admin panel (avoid "admin" for security!)',
            validate: fn (string $value) => match(true) {
                empty($value) => 'Username cannot be empty',
                strlen($value) < 3 => 'Username must be at least 3 characters long',
                default => null
            }
        );

        // Password
        $pass = password(
            label: 'Admin password',
            placeholder: '••••••••',
            hint: 'Must be 7+ characters with both letters and numbers',
            validate: function (string $value) {
                if (empty($value)) {
                    return 'Password cannot be empty';
                }
                if (strlen($value) < 7) {
                    return 'Password must be at least 7 characters long';
                }

                // Magento requires BOTH alphabetic AND numeric
                $hasAlpha = preg_match('/[a-zA-Z]/', $value);
                $hasNumeric = preg_match('/[0-9]/', $value);

                if (!$hasAlpha || !$hasNumeric) {
                    return 'Password must include both alphabetic and numeric characters (required by Magento)';
                }

                return null;
            }
        );

        // Check password strength and show feedback
        $hasLower = preg_match('/[a-z]/', $pass);
        $hasUpper = preg_match('/[A-Z]/', $pass);
        $hasSpecial = preg_match('/[^a-zA-Z0-9]/', $pass);

        if (!$hasLower || !$hasUpper) {
            info('Consider using both uppercase and lowercase letters for better security.');
        } elseif (!$hasSpecial) {
            info('Good password. Consider adding special characters for even better security.');
        } else {
            info('✓ Strong password detected!');
        }

        return [
            'firstName' => $firstName,
            'lastName' => $lastName,
            'email' => $email,
            'username' => $username,
            'password' => $pass
        ];
    }
}
