<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Laminas\EventManager\EventManagerInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\ServiceManager\ServiceManager;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Component\ComponentRegistrarInterface;
use Magento\Framework\DB\Logger\Quiet;
use Magento\Framework\DB\LoggerInterface;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\Locale\Config;
use Magento\Framework\Locale\ConfigInterface;
use Magento\Framework\Setup\Declaration\Schema\SchemaConfig;
use MageOS\Installer\Model\Command\ProcessRunner;
use MageOS\Installer\Model\Command\CronConfigurer;
use MageOS\Installer\Model\Command\EmailConfigurer;
use MageOS\Installer\Model\Command\IndexerConfigurer;
use MageOS\Installer\Model\Command\ModeConfigurer;
use MageOS\Installer\Model\Command\ThemeConfigurer;
use MageOS\Installer\Model\Command\TwoFactorAuthConfigurer;
use MageOS\Installer\Model\Stage\PostInstallConfigStage;
use MageOS\Installer\Model\Theme\HyvaInstaller;
use MageOS\Installer\Model\Validator\PasswordValidator;
use MageOS\Installer\Console\Command\InstallCommand;

return [
    'dependencies' => [
        'auto' => [
            'preferences' => [
                EventManagerInterface::class => 'EventManager',
                ServiceLocatorInterface::class => ServiceManager::class,
                LoggerInterface::class => Quiet::class,
                ConfigInterface::class => Config::class,
                DriverInterface::class => \Magento\Framework\Filesystem\Driver\File::class,
                ComponentRegistrarInterface::class => ComponentRegistrar::class,
            ],
            'types' => [
                SchemaConfig::class => [
                    'parameters' => [
                        'connectionScopes' => [
                            'default',
                            'checkout',
                            'sales'
                        ]
                    ]
                ],
                // Mage-OS Installer: Process Runner and Configurers
                ProcessRunner::class => [],
                CronConfigurer::class => [
                    'parameters' => [
                        'processRunner' => ProcessRunner::class
                    ]
                ],
                EmailConfigurer::class => [
                    'parameters' => [
                        'processRunner' => ProcessRunner::class
                    ]
                ],
                ModeConfigurer::class => [
                    'parameters' => [
                        'processRunner' => ProcessRunner::class
                    ]
                ],
                ThemeConfigurer::class => [
                    'parameters' => [
                        'processRunner' => ProcessRunner::class
                    ]
                ],
                IndexerConfigurer::class => [
                    'parameters' => [
                        'processRunner' => ProcessRunner::class
                    ]
                ],
                TwoFactorAuthConfigurer::class => [
                    'parameters' => [
                        'processRunner' => ProcessRunner::class
                    ]
                ],
                PostInstallConfigStage::class => [
                    'parameters' => [
                        'cronConfigurer' => CronConfigurer::class,
                        'emailConfigurer' => EmailConfigurer::class,
                        'modeConfigurer' => ModeConfigurer::class,
                        'themeConfigurer' => ThemeConfigurer::class,
                        'indexerConfigurer' => IndexerConfigurer::class,
                        'twoFactorAuthConfigurer' => TwoFactorAuthConfigurer::class,
                        'processRunner' => ProcessRunner::class
                    ]
                ],
                HyvaInstaller::class => [
                    'parameters' => [
                        'processRunner' => ProcessRunner::class
                    ]
                ],
                InstallCommand::class => [
                    'parameters' => [
                        'processRunner' => ProcessRunner::class,
                        'cronConfigurer' => CronConfigurer::class,
                        'emailConfigurer' => EmailConfigurer::class,
                        'modeConfigurer' => ModeConfigurer::class,
                        'themeConfigurer' => ThemeConfigurer::class,
                        'indexerConfigurer' => IndexerConfigurer::class,
                        'twoFactorAuthConfigurer' => TwoFactorAuthConfigurer::class,
                    ]
                ],
                PasswordValidator::class => [],
            ],
        ],
    ],
];
