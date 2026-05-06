<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Config;

use function Laravel\Prompts\select;

/**
 * Collects environment type configuration
 */
class EnvironmentConfig
{
    public const ENV_DEVELOPMENT = 'development';
    public const ENV_PRODUCTION = 'production';

    /**
     * Collect environment type
     *
     * @return array{type: string, mageMode: string}
     */
    public function collect(): array
    {
        $environment = select(
            label: 'Installation environment',
            options: [
                self::ENV_DEVELOPMENT => 'Development (debug mode, sample data recommended)',
                self::ENV_PRODUCTION => 'Production (optimized, no sample data)'
            ],
            default: self::ENV_DEVELOPMENT,
            hint: 'Use arrow keys to select, Enter to confirm'
        );

        return [
            'type' => $environment,
            'mageMode' => $environment === self::ENV_PRODUCTION ? 'production' : 'developer'
        ];
    }

    /**
     * Get recommended defaults based on environment
     *
     * @param string $environmentType
     * @return array{
     *     debugMode: bool,
     *     sampleData: bool,
     *     logLevel: string
     * }
     */
    public function getRecommendedDefaults(string $environmentType): array
    {
        if ($environmentType === self::ENV_PRODUCTION) {
            return [
                'debugMode' => false,
                'sampleData' => false,
                'logLevel' => 'error'
            ];
        }

        return [
            'debugMode' => true,
            'sampleData' => true,
            'logLevel' => 'debug'
        ];
    }
}
