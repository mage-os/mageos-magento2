<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;

return [

    'controllers' => [
        'factories' => [
            \Magento\Setup\Controller\Index::class => InvokableFactory::class,
            \Magento\Setup\Controller\LandingInstaller::class => function($container) {
                return new \Magento\Setup\Controller\LandingInstaller(
                    $container->get(\Magento\Framework\App\ProductMetadata::class),
                );
            },

            \Magento\Setup\Controller\LandingUpdater::class => InvokableFactory::class,
            \Magento\Setup\Controller\CreateBackup::class => InvokableFactory::class,
            \Magento\Setup\Controller\CompleteBackup::class => InvokableFactory::class,
            \Magento\Setup\Controller\Navigation::class => function($container) {
                return new \Magento\Setup\Controller\Navigation(
                    $container->get(\Magento\Setup\Model\Navigation::class),
                    $container->get(\Magento\Setup\Model\Cron\Status::class)
                );
            },

            \Magento\Setup\Controller\Home::class => InvokableFactory::class,
            \Magento\Setup\Controller\SelectVersion::class => InvokableFactory::class,
            \Magento\Setup\Controller\License::class => InvokableFactory::class,
            \Magento\Setup\Controller\ReadinessCheckInstaller::class => InvokableFactory::class,
            \Magento\Setup\Controller\ReadinessCheckUpdater::class => InvokableFactory::class,

            \Magento\Setup\Controller\Environment::class => function($container) {
                return new \Magento\Setup\Controller\Environment (
                    $container->get(\Magento\Framework\Setup\FilePermissions::class),
                    $container->get(\Magento\Framework\Filesystem::class),
                    $container->get(\Magento\Setup\Model\CronScriptReadinessCheck::class),
                    $container->get(\Magento\Setup\Model\PhpReadinessCheck::class)
                );
            },

            \Magento\Setup\Controller\DependencyCheck::class => InvokableFactory::class,

            \Magento\Setup\Controller\DatabaseCheck::class => function($container) {
                    return new \Magento\Setup\Controller\DatabaseCheck (
                        $container->get(\Magento\Setup\Validator\DbValidator::class)
                    );
            },

            \Magento\Setup\Controller\UrlCheck::class => function($container) {
                return new \Magento\Setup\Controller\UrlCheck (
                    $container->get(\Magento\Framework\Validator\Url::class)
                );
            },

            \Magento\Setup\Controller\ValidateAdminCredentials::class => function($container) {
                return new \Magento\Setup\Controller\ValidateAdminCredentials (
                    $container->get(\Magento\Setup\Validator\AdminCredentialsValidator::class),
                    $container->get(\Magento\Setup\Model\RequestDataConverter::class)
                );
            },


            \Magento\Setup\Controller\AddDatabase::class => InvokableFactory::class,
            \Magento\Setup\Controller\WebConfiguration::class => InvokableFactory::class,


/************************* elastic search *************************/
            \Magento\Setup\Controller\Elasticsearch::class => function($container) {
                return new \Magento\Setup\Controller\Elasticsearch (
                    $container->get(\Magento\Setup\Validator\DbValidator::class)
                );
            },

            \Magento\Setup\Controller\ElasticsearchCheck::class => function($container) {
                return new \Magento\Setup\Controller\ElasticsearchCheck (
                    $container->get(\Magento\Setup\Validator\DbValidator::class)
                );
            },


            \Magento\Setup\Controller\CustomizeYourStore::class => function($container) {
                return new \Magento\Setup\Controller\CustomizeYourStore (
                    $container->get(\Magento\Framework\Module\FullModuleList::class),
                    $container->get(\Magento\Framework\Setup\Lists::class),
                    $container->get(\Magento\Setup\Model\ObjectManagerProvider::class)
                );
            },

            \Magento\Setup\Controller\CreateAdminAccount::class => function($container) {
                return new \Magento\Setup\Controller\CreateAdminAccount();
            },

            \Magento\Setup\Controller\Install::class => function($container) {
                return new \Magento\Setup\Controller\Install (
                    $container->get(\Magento\Setup\Model\WebLogger::class),
                    $container->get(\Magento\Setup\Model\InstallerFactory::class),
                    $container->get(\Magento\Setup\Model\Installer\ProgressFactory::class),
                    $container->get(\Magento\Framework\Setup\SampleData\State::class),
                    $container->get(\Magento\Framework\App\DeploymentConfig::class),
                    $container->get(\Magento\Setup\Model\RequestDataConverter::class)
                );
            },

            \Magento\Setup\Controller\Success::class => function($container) {
                return new \Magento\Setup\Controller\Success (
                    $container->get(\Magento\Framework\Module\ModuleList::class),
                    $container->get(\Magento\Setup\Model\ObjectManagerProvider::class),
                );
            },

            \Magento\Setup\Controller\Modules::class => function($container) {
                return new \Magento\Setup\Controller\Modules (
                    $container->get(\Magento\Setup\Model\ModuleStatus::class),
                    $container->get(\Magento\Setup\Model\ObjectManagerProvider::class)
                );
            },

            \Magento\Setup\Controller\ModuleGrid::class => InvokableFactory::class,
            \Magento\Setup\Controller\ExtensionGrid::class => InvokableFactory::class,
            \Magento\Setup\Controller\StartUpdater::class => InvokableFactory::class,
            \Magento\Setup\Controller\UpdaterSuccess::class => InvokableFactory::class,
            \Magento\Setup\Controller\BackupActionItems::class => InvokableFactory::class,
            \Magento\Setup\Controller\Maintenance::class => InvokableFactory::class,
            \Magento\Setup\Controller\OtherComponentsGrid::class => InvokableFactory::class,
            \Magento\Setup\Controller\DataOption::class => InvokableFactory::class,
            \Magento\Setup\Controller\Marketplace::class => InvokableFactory::class,
            \Magento\Setup\Controller\SystemConfig::class => InvokableFactory::class,
            \Magento\Setup\Controller\InstallExtensionGrid::class => InvokableFactory::class,
            \Magento\Setup\Controller\UpdateExtensionGrid::class => InvokableFactory::class,
            \Magento\Setup\Controller\MarketplaceCredentials::class => InvokableFactory::class,

            \Magento\Setup\Controller\Session::class => function($container) {
                return new \Magento\Setup\Controller\Session (
                    $container->get(\Laminas\ServiceManager\ServiceManager::class),
                    $container->get(\Magento\Setup\Model\ObjectManagerProvider::class)
                );
            },
        ],
    ],

    'router' => [
        'routes' => [
            'literal' => [
                'type'    => 'Literal',
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => \Magento\Setup\Controller\Index::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'setup' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '[/:controller[/:action]]',
                    'defaults' => [
                        '__NAMESPACE__' => 'Magento\Setup\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ],
                    'constraints' => [
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ],
                ],
            ],
        ],
    ],
];
