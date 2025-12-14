<?php
/**
 * Copyright © Mage-OS, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Theme\Test\Unit\Console\Command;

use Magento\Theme\Console\Command\ThemeRegistrationStatusCommand;
use Magento\Theme\Model\Theme\RegistrationDetector;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class ThemeRegistrationStatusCommandTest extends TestCase
{
    private RegistrationDetector|MockObject $registrationDetector;
    private CommandTester $tester;

    protected function setUp(): void
    {
        $this->registrationDetector = $this->createMock(RegistrationDetector::class);

        $command = new ThemeRegistrationStatusCommand($this->registrationDetector);
        $this->tester = new CommandTester($command);
    }

    public function testExecuteReturnsSuccessWhenAllThemesRegistered(): void
    {
        $this->registrationDetector->method('hasUnregisteredTheme')->willReturn(false);

        $this->tester->execute([]);

        $this->assertEquals(0, $this->tester->getStatusCode());
        $this->assertStringContainsString('All themes are registered', $this->tester->getDisplay());
    }

    public function testExecuteReturnsErrorCodeWhenUnregisteredThemes(): void
    {
        $this->registrationDetector->method('hasUnregisteredTheme')->willReturn(true);
        $this->registrationDetector->method('getMissingThemes')->willReturn([
            'frontend/Vendor/theme1',
            'frontend/Vendor/theme2'
        ]);

        $this->tester->execute([]);

        $this->assertEquals(
            ThemeRegistrationStatusCommand::EXIT_CODE_THEME_UPDATE_REQUIRED,
            $this->tester->getStatusCode()
        );
        $this->assertStringContainsString('Unregistered themes detected', $this->tester->getDisplay());
        $this->assertStringContainsString('frontend/Vendor/theme1', $this->tester->getDisplay());
        $this->assertStringContainsString('frontend/Vendor/theme2', $this->tester->getDisplay());
        $this->assertStringContainsString('setup:upgrade', $this->tester->getDisplay());
    }
}
