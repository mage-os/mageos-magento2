<?php

declare(strict_types=1);

namespace MageOS\Installer\Test\Unit\MageOS\Installer\Model\Command;

use MageOS\Installer\Model\Command\ProcessResult;
use MageOS\Installer\Model\Command\ProcessRunner;
use MageOS\Installer\Model\Command\ThemeConfigurer;
use MageOS\Installer\Model\VO\ThemeConfiguration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Unit tests for ThemeConfigurer
 */
class ThemeConfigurerTest extends TestCase
{
    /** @var ProcessRunner */
    private ProcessRunner $processRunnerMock;
    /** @var ThemeConfigurer */
    private ThemeConfigurer $configurer;
    /** @var BufferedOutput */
    private BufferedOutput $output;

    protected function setUp(): void
    {
        parent::setUp();
        $this->processRunnerMock = $this->createMock(ProcessRunner::class);
        $this->configurer = new ThemeConfigurer($this->processRunnerMock);
        $this->output = new BufferedOutput();
    }

    public function testApplyReturnsTrueWhenNoThemeSelected(): void
    {
        $themeConfig = new ThemeConfiguration(install: false);

        $result = $this->configurer->apply($themeConfig, '/var/www/magento', $this->output);

        $this->assertTrue($result);
    }

    public function testApplyReturnsTrueWhenThemeIsEmpty(): void
    {
        $themeConfig = new ThemeConfiguration(install: true, theme: '');

        $result = $this->configurer->apply($themeConfig, '/var/www/magento', $this->output);

        $this->assertTrue($result);
    }

    public function testApplyGetsThemeList(): void
    {
        $themeConfig = new ThemeConfiguration(install: true, theme: 'hyva-default');
        $themeListOutput = "| 4 | frontend | Hyva/default |\n";

        $this->processRunnerMock->expects($this->exactly(3)) // theme:list, config:set, cache:clean
            ->method('runMagentoCommand')
            ->willReturnCallback(function (array $command) use ($themeListOutput) {
                if (in_array('theme:list', $command, true)) {
                    return new ProcessResult(true, $themeListOutput);
                }
                return new ProcessResult(true, '');
            });

        $this->configurer->apply($themeConfig, '/var/www/magento', $this->output);
    }

    public function testApplyCallsConfigSetWithThemeId(): void
    {
        $themeConfig = new ThemeConfiguration(install: true, theme: 'hyva-default');
        $themeListOutput = "| 4 | frontend | Hyva/default |\n";

        $this->processRunnerMock->expects($this->exactly(3))
            ->method('runMagentoCommand')
            ->willReturnCallback(function (array $command) use ($themeListOutput) {
                if (in_array('theme:list', $command, true)) {
                    return new ProcessResult(true, $themeListOutput);
                }
                if (in_array('design/theme/theme_id', $command, true)) {
                    return new ProcessResult(true, 'Config saved');
                }
                if (in_array('cache:clean', $command, true)) {
                    return new ProcessResult(true, 'Cache cleaned');
                }
                return new ProcessResult(false, '', 'Unknown command');
            });

        $result = $this->configurer->apply($themeConfig, '/var/www/magento', $this->output);

        $this->assertTrue($result);
    }

    public function testApplyClearsCacheAfterThemeApplication(): void
    {
        $themeConfig = new ThemeConfiguration(install: true, theme: 'hyva');
        $themeListOutput = "| 5 | frontend | Hyva/default |\n";

        $cacheCleanCalled = false;

        $this->processRunnerMock->method('runMagentoCommand')
            ->willReturnCallback(function (array $command) use ($themeListOutput, &$cacheCleanCalled) {
                if (in_array('theme:list', $command, true)) {
                    return new ProcessResult(true, $themeListOutput);
                }
                if (in_array('cache:clean', $command, true)) {
                    $cacheCleanCalled = true;
                    return new ProcessResult(true, '');
                }
                return new ProcessResult(true, '');
            });

        $this->configurer->apply($themeConfig, '/var/www/magento', $this->output);

        $this->assertTrue($cacheCleanCalled, 'Cache should be cleared after theme application');
    }

    public function testApplyReturnsFalseWhenThemeNotFound(): void
    {
        $themeConfig = new ThemeConfiguration(install: true, theme: 'nonexistent-theme');
        $themeListOutput = "| 4 | frontend | Luma |\n";

        $this->processRunnerMock->method('runMagentoCommand')
            ->willReturn(new ProcessResult(true, $themeListOutput));

        $result = $this->configurer->apply($themeConfig, '/var/www/magento', $this->output);

        $this->assertFalse($result);
    }

    public function testApplyHandlesThemeListFailure(): void
    {
        $themeConfig = new ThemeConfiguration(install: true, theme: 'hyva');

        $this->processRunnerMock->method('runMagentoCommand')
            ->willReturn(new ProcessResult(false, '', 'Command failed'));

        $result = $this->configurer->apply($themeConfig, '/var/www/magento', $this->output);

        $this->assertFalse($result);
    }
}
