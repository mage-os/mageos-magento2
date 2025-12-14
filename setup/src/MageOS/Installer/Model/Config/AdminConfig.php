<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Config;

use MageOS\Installer\Model\Validator\EmailValidator;
use MageOS\Installer\Model\Validator\PasswordValidator;

use function Laravel\Prompts\info;
use function Laravel\Prompts\note;
use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

/**
 * Collects admin account configuration with Laravel Prompts
 */
class AdminConfig
{
    public function __construct(
        private readonly EmailValidator $emailValidator,
        private readonly PasswordValidator $passwordValidator
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
            hint: $this->passwordValidator->getRequirementsHint(),
            validate: fn (string $value) => $this->passwordValidator->validate($value)
        );

        // Show password strength feedback
        info($this->passwordValidator->getStrengthFeedback($pass));

        return [
            'firstName' => $firstName,
            'lastName' => $lastName,
            'email' => $email,
            'username' => $username,
            'password' => $pass
        ];
    }
}
