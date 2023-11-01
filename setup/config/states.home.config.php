<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

$base = basename($_SERVER['SCRIPT_FILENAME']);

return [
    'navUpdater' => [
        [
            'id'          => 'root',
            'step'        => 0,
            'views'       => ['root' => []],
        ],
        [
            'id'          => 'root.home',
            'url'         => 'home',
            'title'       => 'Mage-OS Setup Wizard',
            'templateUrl' => "$base/home",
            'header'      => 'Home',
            'nav'         => false,
            'default'     => true,
            'noMenu'      => true,
            'order'       => -1,
        ],
        [
            'id'          => 'root.install',
            'url'         => 'install-extension-grid',
            'templateUrl' => "$base/install-extension-grid",
            'title'       => "Extension Manager",
            'controller'  => 'installExtensionGridController',
            'nav'         => false,
            'noMenu'      => true,
            'order'       => 1,
            'type'        => 'install',
            'wrapper'     => 1,
            'header'      => 'Ready to Install'
        ],
    ],
];
