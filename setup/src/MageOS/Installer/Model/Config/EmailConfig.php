<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Config;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\note;
use function Laravel\Prompts\password;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

/**
 * Collects email/SMTP configuration
 */
class EmailConfig
{
    /**
     * Collect email configuration
     *
     * @return array{
     *     configure: bool,
     *     transport: string,
     *     host: string|null,
     *     port: int|null,
     *     username: string|null,
     *     password: string|null,
     *     auth: string|null
     * }
     */
    public function collect(): array
    {
        note('Email Configuration');

        info('Email is needed for:');
        info('• Order confirmations');
        info('• Customer registration');
        info('• Password resets');
        info('• Admin notifications');

        $configure = confirm(
            label: 'Configure email/SMTP now?',
            default: false,
            hint: 'Can be configured later in admin panel'
        );

        if (!$configure) {
            info('Skipping email configuration (will use PHP mail() by default)');
            return [
                'configure' => false,
                'transport' => 'sendmail',
                'host' => null,
                'port' => null,
                'username' => null,
                'password' => null,
                'auth' => null
            ];
        }

        $transport = select(
            label: 'Email transport',
            options: [
                'smtp' => 'SMTP (recommended for reliability)',
                'sendmail' => 'Sendmail (uses server mail command)',
            ],
            default: 'smtp',
            hint: 'How to send emails'
        );

        if ($transport === 'sendmail') {
            return [
                'configure' => true,
                'transport' => 'sendmail',
                'host' => null,
                'port' => null,
                'username' => null,
                'password' => null,
                'auth' => null
            ];
        }

        // SMTP configuration
        $host = text(
            label: 'SMTP host',
            placeholder: 'smtp.example.com',
            hint: 'SMTP server hostname',
            validate: fn ($value) => empty($value) ? 'SMTP host cannot be empty' : null
        );

        $port = (int)text(
            label: 'SMTP port',
            default: '587',
            placeholder: '587',
            hint: '587 (TLS) or 465 (SSL) or 25 (unencrypted)',
            validate: fn ($value) => !is_numeric($value) ? 'Port must be a number' : null
        );

        $auth = select(
            label: 'SMTP authentication',
            options: [
                'login' => 'LOGIN (username/password)',
                'plain' => 'PLAIN (username/password)',
                'none' => 'None (no authentication)'
            ],
            default: 'login',
            hint: 'Authentication method'
        );

        $username = null;
        $pass = null;

        if ($auth !== 'none') {
            $username = text(
                label: 'SMTP username',
                placeholder: 'user@example.com',
                hint: 'Username for SMTP authentication'
            );

            $pass = password(
                label: 'SMTP password',
                hint: 'Password for SMTP authentication'
            );
        }

        return [
            'configure' => true,
            'transport' => 'smtp',
            'host' => $host,
            'port' => $port,
            'username' => $username,
            'password' => $pass,
            'auth' => $auth === 'none' ? null : $auth
        ];
    }
}
