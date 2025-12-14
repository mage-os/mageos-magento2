<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Validator;

/**
 * Validates passwords according to Magento requirements
 */
class PasswordValidator
{
    /**
     * Validate password meets Magento requirements
     *
     * Magento requires:
     * - At least 7 characters
     * - Both alphabetic AND numeric characters
     *
     * @param string $password
     * @return string|null Error message or null if valid
     */
    public function validate(string $password): ?string
    {
        if (empty($password)) {
            return 'Password cannot be empty';
        }

        if (strlen($password) < 7) {
            return 'Password must be at least 7 characters long';
        }

        $hasAlpha = preg_match('/[a-zA-Z]/', $password);
        $hasNumeric = preg_match('/[0-9]/', $password);

        if (!$hasAlpha || !$hasNumeric) {
            return 'Password must include both alphabetic and numeric characters (required by Magento)';
        }

        return null; // Valid
    }

    /**
     * Get password strength feedback
     *
     * @param string $password
     * @return string Strength feedback message
     */
    public function getStrengthFeedback(string $password): string
    {
        $hasLower = preg_match('/[a-z]/', $password);
        $hasUpper = preg_match('/[A-Z]/', $password);
        $hasSpecial = preg_match('/[^a-zA-Z0-9]/', $password);

        if (!$hasLower || !$hasUpper) {
            return 'Consider using both uppercase and lowercase letters for better security.';
        }

        if (!$hasSpecial) {
            return 'Good password. Consider adding special characters for even better security.';
        }

        return '✓ Strong password detected!';
    }

    /**
     * Get password requirements hint
     *
     * @return string
     */
    public function getRequirementsHint(): string
    {
        return 'Must be 7+ characters with both letters and numbers';
    }
}
