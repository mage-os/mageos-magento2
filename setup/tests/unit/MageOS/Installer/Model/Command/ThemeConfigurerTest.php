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
final class ThemeConfigurerTest extends TestCase
{
    private ProcessRunner $processRunnerMock;
    private ThemeConfigurer $configurer;
    private BufferedOutput $output;

    protected function setUp(): void
    {
        parent::setUp();
        $this->processRunnerMock = $this->createMock(ProcessRunner::class);
        $this->configurer = new ThemeConfigurer($this->processRunnerMock);
        $this->output = new BufferedOutput();
    }

    public function test_apply_returns_true_when_no_theme_selected(): void
    {
        $themeConfig = new ThemeConfiguration(install: false);

        $result = $this->configurer->apply($themeConfig, '/var/www/magento', $this->output);

        $this->assertTrue($result);
    }

    public function test_apply_returns_true_when_theme_is_empty(): void
    {
        $themeConfig = new ThemeConfiguration(install: true, theme: '');

        $result = $this->configurer->apply($themeConfig, '/var/www/magento', $this->output);

        $this->assertTrue($result);
    }

    public function test_apply_gets_theme_list(): void
    {
        $themeConfig = new ThemeConfiguration(install: true, theme: 'hyva-default');
        $themeListOutput = "| 4 | frontend | Hyva/default |\n";

        $this->processRunnerMock->expects($this->exactly(3)) // theme:list, config:set, cache:clean
            ->method('runMagentoCommand')
            ->willReturnCallback(function ($command) use ($themeListOutput) {
                if (str_contains($command, 'theme:list')) {
                    return new ProcessResult(true, $themeListOutput);
                }
                return new ProcessResult(true, '');
            });

        $this->configurer->apply($themeConfig, '/var/www/magento', $this->output);
    }

    public function test_apply_calls_config_set_with_theme_id(): void
    {
        $themeConfig = new ThemeConfiguration(install: true, theme: 'hyva-default');
        $themeListOutput = "| 4 | frontend | Hyva/default |\n";

        $this->processRunnerMock->expects($this->exactly(3))
            ->method('runMagentoCommand')
            ->willReturnCallback(function ($command) use ($themeListOutput) {
                if (str_contains($command, 'theme:list')) {
                    return new ProcessResult(true, $themeListOutput);
                }
                if (str_contains($command, 'config:set design/theme/theme_id 4')) {
                    return new ProcessResult(true, 'Config saved');
                }
                if (str_contains($command, 'cache:clean')) {
                    return new ProcessResult(true, 'Cache cleaned');
                }
                return new ProcessResult(false, '', 'Unknown command');
            });

        $result = $this->configurer->apply($themeConfig, '/var/www/magento', $this->output);

        $this->assertTrue($result);
    }

    public function test_apply_clears_cache_after_theme_application(): void
    {
        $themeConfig = new ThemeConfiguration(install: true, theme: 'hyva');
        $themeListOutput = "| 5 | frontend | Hyva/default |\n";

        $cacheCleanCalled = false;

        $this->processRunnerMock->method('runMagentoCommand')
            ->willReturnCallback(function ($command) use ($themeListOutput, &$cacheCleanCalled) {
                if (str_contains($command, 'theme:list')) {
                    return new ProcessResult(true, $themeListOutput);
                }
                if (str_contains($command, 'cache:clean')) {
                    $cacheCleanCalled = true;
                    return new ProcessResult(true, '');
                }
                return new ProcessResult(true, '');
            });

        $this->configurer->apply($themeConfig, '/var/www/magento', $this->output);

        $this->assertTrue($cacheCleanCalled, 'Cache should be cleared after theme application');
    }

    public function test_apply_returns_false_when_theme_not_found(): void
    {
        $themeConfig = new ThemeConfiguration(install: true, theme: 'nonexistent-theme');
        $themeListOutput = "| 4 | frontend | Luma |\n";

        $this->processRunnerMock->method('runMagentoCommand')
            ->willReturn(new ProcessResult(true, $themeListOutput));

        $result = $this->configurer->apply($themeConfig, '/var/www/magento', $this->output);

        $this->assertFalse($result);
    }

    public function test_apply_handles_theme_list_failure(): void
    {
        $themeConfig = new ThemeConfiguration(install: true, theme: 'hyva');

        $this->processRunnerMock->method('runMagentoCommand')
            ->willReturn(new ProcessResult(false, '', 'Command failed'));

        $result = $this->configurer->apply($themeConfig, '/var/www/magento', $this->output);

        $this->assertFalse($result);
    }
}
