<?php

declare(strict_types=1);

namespace MageOS\Installer\Test\Unit\MageOS\Installer\Model\Command;

use MageOS\Installer\Model\Command\ModeConfigurer;
use MageOS\Installer\Model\Command\ProcessResult;
use MageOS\Installer\Model\Command\ProcessRunner;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Unit tests for ModeConfigurer
 */
class ModeConfigurerTest extends TestCase
{
    private ProcessRunner $processRunnerMock;
    private ModeConfigurer $configurer;
    private BufferedOutput $output;

    protected function setUp(): void
    {
        parent::setUp();
        $this->processRunnerMock = $this->createMock(ProcessRunner::class);
        $this->configurer = new ModeConfigurer($this->processRunnerMock);
        $this->output = new BufferedOutput();
    }

    public function test_set_mode_returns_true_on_success(): void
    {
        $successResult = new ProcessResult(true, 'Mode set');

        $this->processRunnerMock->method('runMagentoCommand')
            ->willReturn($successResult);

        $result = $this->configurer->setMode('production', '/var/www/magento', $this->output);

        $this->assertTrue($result);
    }

    public function test_set_mode_returns_false_on_failure(): void
    {
        $failureResult = new ProcessResult(false, '', 'Failed');

        $this->processRunnerMock->method('runMagentoCommand')
            ->willReturn($failureResult);

        $result = $this->configurer->setMode('production', '/var/www/magento', $this->output);

        $this->assertFalse($result);
    }

    public function test_set_mode_calls_deploy_mode_set_command(): void
    {
        $successResult = new ProcessResult(true, '');

        $this->processRunnerMock->expects($this->once())
            ->method('runMagentoCommand')
            ->with('deploy:mode:set production', $this->anything(), $this->anything())
            ->willReturn($successResult);

        $this->configurer->setMode('production', '/var/www/magento', $this->output);
    }

    public function test_set_mode_uses_120_second_timeout(): void
    {
        $successResult = new ProcessResult(true, '');

        $this->processRunnerMock->expects($this->once())
            ->method('runMagentoCommand')
            ->with($this->anything(), $this->anything(), 120)
            ->willReturn($successResult);

        $this->configurer->setMode('developer', '/var/www/magento', $this->output);
    }

    public function test_set_mode_supports_different_modes(): void
    {
        $modes = ['developer', 'production', 'default'];

        foreach ($modes as $mode) {
            $successResult = new ProcessResult(true, '');

            // Create fresh mock for each mode
            $processRunner = $this->createMock(ProcessRunner::class);
            $processRunner->expects($this->once())
                ->method('runMagentoCommand')
                ->with("deploy:mode:set {$mode}", $this->anything(), $this->anything())
                ->willReturn($successResult);

            $configurer = new ModeConfigurer($processRunner);
            $result = $configurer->setMode($mode, '/var/www/magento', new BufferedOutput());

            $this->assertTrue($result);
        }
    }

    public function test_set_mode_displays_success_message(): void
    {
        $successResult = new ProcessResult(true, '');

        $this->processRunnerMock->method('runMagentoCommand')
            ->willReturn($successResult);

        $this->configurer->setMode('production', '/var/www/magento', $this->output);

        $outputContent = $this->output->fetch();
        $this->assertStringContainsString('Magento mode set to production', $outputContent);
    }

    public function test_set_mode_displays_manual_instructions_on_failure(): void
    {
        $failureResult = new ProcessResult(false, '', 'Error');

        $this->processRunnerMock->method('runMagentoCommand')
            ->willReturn($failureResult);

        $this->configurer->setMode('production', '/var/www/magento', $this->output);

        $outputContent = $this->output->fetch();
        $this->assertStringContainsString('Run manually', $outputContent);
        $this->assertStringContainsString('deploy:mode:set production', $outputContent);
    }
}
