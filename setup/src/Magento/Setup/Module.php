<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup;

use Magento\Framework\Setup\Mvc\MvcEvent;

/**
 * Native module declaration
 */
class Module
{
    /**
     * Native bootstrap method
     */
    public function onBootstrap(MvcEvent $e)
    {
        // Simplified native bootstrap for CLI setup commands
        // Most of the original functionality (headers, routing) is not needed for setup commands
        // The main purpose is to initialize basic services for compatibility
    }

    /**
     * @inheritDoc
     */
    public function getConfig()
    {
        // phpcs:disable
        $result = array_merge_recursive(
            include __DIR__ . '/../../../config/module.config.php',
            include __DIR__ . '/../../../config/di.config.php',
        );
        // phpcs:enable
        return $result;
    }
}
