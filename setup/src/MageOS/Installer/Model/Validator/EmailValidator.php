<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Validator;

/**
 * Validates email addresses
 */
class EmailValidator
{
    /**
     * Validate email address
     *
     * @param string $email
     * @return array{valid: bool, error: string|null}
     */
    public function validate(string $email): array
    {
        if (empty($email)) {
            return [
                'valid' => false,
                'error' => 'Email address cannot be empty'
            ];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'valid' => false,
                'error' => 'Invalid email address format'
            ];
        }

        return [
            'valid' => true,
            'error' => null
        ];
    }
}
