<?php

declare(strict_types=1);

namespace MageOS\Installer\Test\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Smoke test to verify PHPUnit infrastructure is working
 */
final class SmokeTest extends TestCase
{
    public function test_phpunit_is_working(): void
    {
        $this->assertTrue(true, 'PHPUnit infrastructure is operational');
    }

    public function test_autoloader_is_working(): void
    {
        $this->assertTrue(
            class_exists(\MageOS\Installer\Model\InstallationContext::class),
            'Autoloader can find installer classes'
        );
    }
}
