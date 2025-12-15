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
    public function test_get_name_returns_magento_installation(): void
    {
        $app = $this->createMock(Application::class);
        $stage = new \MageOS\Installer\Model\Stage\MagentoInstallationStage($app);

        $this->assertEquals('Magento Installation', $stage->getName());
    }

    public function test_can_go_back_returns_false(): void
    {
        $app = $this->createMock(Application::class);
        $stage = new \MageOS\Installer\Model\Stage\MagentoInstallationStage($app);

        $this->assertFalse($stage->canGoBack(), 'Cannot go back once installation starts');
    }

    public function test_get_progress_weight_returns_high_value(): void
    {
        $app = $this->createMock(Application::class);
        $stage = new \MageOS\Installer\Model\Stage\MagentoInstallationStage($app);

        $this->assertEquals(10, $stage->getProgressWeight(), 'Installation is heavy operation');
    }

    public function test_should_skip_returns_false(): void
    {
        $app = $this->createMock(Application::class);
        $stage = new \MageOS\Installer\Model\Stage\MagentoInstallationStage($app);
        $context = TestDataBuilder::validInstallationContext();

        $this->assertFalse($stage->shouldSkip($context), 'Installation should never be skipped');
    }
}
