<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Config;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\note;

/**
 * Collects cron configuration
 */
class CronConfig
{
    /**
     * Collect cron configuration
     *
     * @return array{configure: bool}
     */
    public function collect(): array
    {
        note('Cron Configuration');

        info('Magento requires cron for:');
        info('• Reindexing');
        info('• Email queue processing');
        info('• Scheduled tasks');
        info('• Cache cleaning');

        $configure = confirm(
            label: 'Configure cron now?',
            default: true,
            hint: 'Highly recommended - Magento needs cron to function properly'
        );

        return [
            'configure' => $configure
        ];
    }
}
