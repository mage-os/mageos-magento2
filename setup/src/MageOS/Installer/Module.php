<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer;

use Laminas\ModuleManager\Feature\ConfigProviderInterface;

/**
 * Laminas module declaration for MageOS Installer
 */
class Module implements ConfigProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getConfig()
    {
        return [];
    }
}
