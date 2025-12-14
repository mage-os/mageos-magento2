<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Config;

use MageOS\Installer\Model\Validator\UrlValidator;

use function Laravel\Prompts\note;
use function Laravel\Prompts\text;
use function Laravel\Prompts\warning;

/**
 * Collects backend configuration with Laravel Prompts
 */
class BackendConfig
{
    public function __construct(
        private readonly UrlValidator $urlValidator
    ) {
    }

    /**
     * Collect backend configuration
     *
     * @return array{frontname: string}
     */
    public function collect(): array
    {
        note('Backend Configuration');

        // Backend frontname (admin path)
        $frontname = text(
            label: 'Backend admin path',
            default: 'admin',
            placeholder: 'admin',
            hint: 'Custom path recommended for security (e.g., "admin_xyz")',
            validate: fn (string $value) => match (true) {
                empty($value) => 'Admin path cannot be empty',
                !preg_match('/^[a-zA-Z0-9_-]+$/', $value) => 'Admin path can only contain letters, numbers, underscores, and hyphens',
                default => null
            }
        );

        // Show security warning if using default
        if ($frontname === 'admin') {
            warning('Using default "admin" path is not recommended for security. Consider using a custom path.');
        }

        return [
            'frontname' => $frontname
        ];
    }
}
