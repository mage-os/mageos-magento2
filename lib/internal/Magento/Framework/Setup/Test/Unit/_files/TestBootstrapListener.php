<?php
declare(strict_types=1);

namespace Magento\Framework\Setup\Mvc;

use Magento\Framework\Setup\Mvc\MvcApplication;
use Magento\Framework\Setup\Mvc\MvcEvent;

final class TestBootstrapListener
{
    public static bool $bootstrapped = false;

    public function onBootstrap(MvcEvent $event): void
    {
        self::$bootstrapped = $event->getApplication() instanceof MvcApplication;
    }
}


