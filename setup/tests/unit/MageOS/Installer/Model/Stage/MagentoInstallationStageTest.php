<?php

declare(strict_types=1);

namespace MageOS\Installer\Test\Unit\MageOS\Installer\Model\Stage;

use MageOS\Installer\Model\InstallationContext;
use MageOS\Installer\Test\Util\TestDataBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Unit tests for MagentoInstallationStage
 *
 * Note: Full installation testing requires integration tests.
 * These unit tests verify the stage structure and logic.
 */
class MagentoInstallationStageTest extends TestCase
{
    public function testGetNameReturnsMagentoInstallation(): void
    {
        $app = $this->createMock(Application::class);
        $stage = new \MageOS\Installer\Model\Stage\MagentoInstallationStage($app);

        $this->assertEquals('Magento Installation', $stage->getName());
    }

    public function testCanGoBackReturnsFalse(): void
    {
        $app = $this->createMock(Application::class);
        $stage = new \MageOS\Installer\Model\Stage\MagentoInstallationStage($app);

        $this->assertFalse($stage->canGoBack(), 'Cannot go back once installation starts');
    }

    public function testGetProgressWeightReturnsHighValue(): void
    {
        $app = $this->createMock(Application::class);
        $stage = new \MageOS\Installer\Model\Stage\MagentoInstallationStage($app);

        $this->assertEquals(10, $stage->getProgressWeight(), 'Installation is heavy operation');
    }

    public function testShouldSkipReturnsFalse(): void
    {
        $app = $this->createMock(Application::class);
        $stage = new \MageOS\Installer\Model\Stage\MagentoInstallationStage($app);
        $context = TestDataBuilder::validInstallationContext();

        $this->assertFalse($stage->shouldSkip($context), 'Installation should never be skipped');
    }
}
