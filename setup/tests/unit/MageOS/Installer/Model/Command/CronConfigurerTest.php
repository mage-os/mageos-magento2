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
    /** @var ProcessRunner */
    private ProcessRunner $processRunnerMock;
    /** @var CronConfigurer */
    private CronConfigurer $configurer;
    /** @var BufferedOutput */
    private BufferedOutput $output;

    protected function setUp(): void
    {
        parent::setUp();
        $this->processRunnerMock = $this->createMock(ProcessRunner::class);
        $this->configurer = new CronConfigurer($this->processRunnerMock);
        $this->output = new BufferedOutput();
    }

    public function testConfigureReturnsTrueOnSuccess(): void
    {
        $successResult = new ProcessResult(true, 'Cron configured');

        $this->processRunnerMock->expects($this->once())
            ->method('runMagentoCommand')
            ->with(['cron:install'], '/var/www/magento', 30)
            ->willReturn($successResult);

        $result = $this->configurer->configure('/var/www/magento', $this->output);

        $this->assertTrue($result);
    }

    public function testConfigureReturnsFalseOnFailure(): void
    {
        $failureResult = new ProcessResult(false, '', 'Command failed');

        $this->processRunnerMock->method('runMagentoCommand')
            ->willReturn($failureResult);

        $result = $this->configurer->configure('/var/www/magento', $this->output);

        $this->assertFalse($result);
    }

    public function testConfigureCallsCronInstallCommand(): void
    {
        $successResult = new ProcessResult(true, '');

        $this->processRunnerMock->expects($this->once())
            ->method('runMagentoCommand')
            ->with(['cron:install'], $this->anything(), $this->anything())
            ->willReturn($successResult);

        $this->configurer->configure('/var/www/magento', $this->output);
    }

    public function testConfigureUses30SecondTimeout(): void
    {
        $successResult = new ProcessResult(true, '');

        $this->processRunnerMock->expects($this->once())
            ->method('runMagentoCommand')
            ->with($this->anything(), $this->anything(), 30)
            ->willReturn($successResult);

        $this->configurer->configure('/var/www/magento', $this->output);
    }

    public function testConfigureDisplaysSuccessMessage(): void
    {
        $successResult = new ProcessResult(true, '');

        $this->processRunnerMock->method('runMagentoCommand')
            ->willReturn($successResult);

        $this->configurer->configure('/var/www/magento', $this->output);

        $outputContent = $this->output->fetch();
        $this->assertStringContainsString('Cron configured successfully', $outputContent);
    }

    public function testConfigureDisplaysManualInstructionsOnFailure(): void
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
