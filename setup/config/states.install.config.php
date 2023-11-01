<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

$base = basename($_SERVER['SCRIPT_FILENAME']);

return [
    'navInstallerTitles' => [
        'install'    => 'Magento Installer',
    ],
    'navInstaller' => [
        [
            'id'          => 'root',
            'step'        => 0,
            'views'       => ['root' => []],
        ],
        [
            'id'          => 'root.license',
            'url'         => 'license',
            'templateUrl' => "$base/license",
            'title'       => 'License',
            'main'        => true,
            'nav'         => false,
            'order'       => -1,
            'type'        => 'install'
        ],
        [
            'id'          => 'root.landing-install',
            'url'         => 'landing-install',
            'templateUrl' => "$base/landing-installer",
            'title'       => 'Mage-OS Installation',
            'controller'  => 'landingController',
            'main'        => true,
            'default'     => true,
            'order'       => 0,
            'type'        => 'install'
        ],
        [
            'id'          => 'root.readiness-check-install',
            'url'         => 'readiness-check-install',
            'templateUrl' => "{$base}/readiness-check-installer",
            'title'       => "Readiness \n Check",
            'header'      => 'Step 1: Readiness Check',
            'nav'         => true,
            'order'       => 1,
            'type'        => 'install'
        ],
        [
            'id'          => 'root.readiness-check-install.progress',
            'url'         => 'readiness-check-install/progress',
            'templateUrl' => "{$base}/readiness-check-installer/progress",
            'title'       => 'Readiness Check',
            'header'      => 'Step 1: Readiness Check',
            'controller'  => 'readinessCheckController',
            'nav'         => false,
            'order'       => 2,
            'type'        => 'install'
        ],
        [
            'id'          => 'root.add-database',
            'url'         => 'add-database',
            'templateUrl' => "{$base}/add-database",
            'title'       => "MySQL \n Configuration",
            'header'      => 'Step 2: MySQL Configuration',
            'controller'  => 'addDatabaseController',
            'nav'         => true,
            'validate'    => true,
            'order'       => 3,
            'type'        => 'install'
        ],
        [
            'id'          => 'root.web-configuration',
            'url'         => 'web-configuration',
            'templateUrl' => "{$base}/web-configuration",
            'title'       => "Web \n Configuration",
            'header'      => 'Step 3: Web Configuration',
            'controller'  => 'webConfigurationController',
            'nav'         => true,
            'validate'    => true,
            'order'       => 4,
            'type'        => 'install'
        ],
        [
            'id'          => 'root.elasticsearch',
            'url'         => 'elasticsearch',
            'templateUrl' => "{$base}/elasticsearch",
            'title'       => "NoSQL \n Configuration",
            'header'      => 'Step 4: NoSQL',
            'controller'  => 'elasticsearchController',
            'nav'         => true,
            'validate'    => true,
            'order'       => 5,
            'type'        => 'install'
        ],
        [
            'id'          => 'root.customize-your-store',
            'url'         => 'customize-your-store',
            'templateUrl' => "{$base}/customize-your-store",
            'title'       => "Customize \n Your Store",
            'header'      => 'Step 5: Customize Your Store',
            'controller'  => 'customizeYourStoreController',
            'nav'         => true,
            'order'       => 6,
            'type'        => 'install'
        ],
        [
            'id'          => 'root.create-admin-account',
            'url'         => 'create-admin-account',
            'templateUrl' => "{$base}/create-admin-account",
            'title'       => "Create \n Admin Account",
            'header'      => 'Step 6: Create Admin Account',
            'controller'  => 'createAdminAccountController',
            'nav'         => true,
            'validate'    => true,
            'order'       => 7,
            'type'        => 'install'
        ],
        [
            'id'          => 'root.install',
            'url'         => 'install',
            'templateUrl' => "{$base}/install",
            'title'       => 'Install',
            'header'      => 'Step 7: Install',
            'controller'  => 'installController',
            'nav'         => true,
            'order'       => 8,
            'type'        => 'install'
        ],
        [
            'id'          => 'root.success',
            'url'         => 'success',
            'templateUrl' => "{$base}/success",
            'title'       => 'Success',
            'controller'  => 'successController',
            'main'        => true,
            'order'       => 9,
            'type'        => 'install'
        ],
    ],
];
