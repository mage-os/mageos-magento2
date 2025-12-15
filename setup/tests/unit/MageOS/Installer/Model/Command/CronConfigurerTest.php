<?php

declare(strict_types=1);

namespace MageOS\Installer\Test\Unit\MageOS\Installer\Model\Command;

use MageOS\Installer\Model\Command\CronConfigurer;
use MageOS\Installer\Model\Command\ProcessResult;
use MageOS\Installer\Model\Command\ProcessRunner;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Unit tests for CronConfigurer
 */
class CronConfigurerTest extends TestCase
{
    private ProcessRunner $processRunnerMock;
    private CronConfigurer $configurer;
    private BufferedOutput $output;

    protected function setUp(): void
    {
        parent::setUp();
        $this->processRunnerMock = $this->createMock(ProcessRunner::class);
        $this->configurer = new CronConfigurer($this->processRunnerMock);
        $this->output = new BufferedOutput();
    }

    public function test_configure_returns_true_on_success(): void
    {
        $successResult = new ProcessResult(true, 'Cron configured');

        $this->processRunnerMock->expects($this->once())
            ->method('runMagentoCommand')
            ->with('cron:install', '/var/www/magento', 30)
            ->willReturn($successResult);

        $result = $this->configurer->configure('/var/www/magento', $this->output);

        $this->assertTrue($result);
    }

    public function test_configure_returns_false_on_failure(): void
    {
        $failureResult = new ProcessResult(false, '', 'Command failed');

        $this->processRunnerMock->method('runMagentoCommand')
            ->willReturn($failureResult);

        $result = $this->configurer->configure('/var/www/magento', $this->output);

        $this->assertFalse($result);
    }

    public function test_configure_calls_cron_install_command(): void
    {
        $successResult = new ProcessResult(true, '');

        $this->processRunnerMock->expects($this->once())
            ->method('runMagentoCommand')
            ->with('cron:install', $this->anything(), $this->anything())
            ->willReturn($successResult);

        $this->configurer->configure('/var/www/magento', $this->output);
    }

    public function test_configure_uses_30_second_timeout(): void
    {
        $successResult = new ProcessResult(true, '');

        $this->processRunnerMock->expects($this->once())
            ->method('runMagentoCommand')
            ->with($this->anything(), $this->anything(), 30)
            ->willReturn($successResult);

        $this->configurer->configure('/var/www/magento', $this->output);
    }

    public function test_configure_displays_success_message(): void
    {
        $successResult = new ProcessResult(true, '');

        $this->processRunnerMock->method('runMagentoCommand')
            ->willReturn($successResult);

        $this->configurer->configure('/var/www/magento', $this->output);

        $outputContent = $this->output->fetch();
        $this->assertStringContainsString('Cron configured successfully', $outputContent);
    }

    public function test_configure_displays_manual_instructions_on_failure(): void
    {
        $failureResult = new ProcessResult(false, '', 'Error');

        $this->processRunnerMock->method('runMagentoCommand')
            ->willReturn($failureResult);

        $this->configurer->configure('/var/www/magento', $this->output);

        $outputContent = $this->output->fetch();
        $this->assertStringContainsString('Configure manually', $outputContent);
        $this->assertStringContainsString('crontab', $outputContent);
    }
}
