<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Config;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\note;

/**
 * Collects sample data configuration with Laravel Prompts
 */
class SampleDataConfig
{
    /**
     * Collect sample data configuration
     *
     * @return array{install: bool}
     */
    public function collect(): array
    {
        note('Optional Features');

        $installSampleData = confirm(
            label: 'Install sample data?',
            default: false,
            hint: 'Sample data is useful for development and testing'
        );

        return [
            'install' => $installSampleData
        ];
    }
}
