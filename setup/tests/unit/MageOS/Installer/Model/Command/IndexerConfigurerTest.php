<?php

declare(strict_types=1);

namespace MageOS\Installer\Test\Unit\MageOS\Installer\Model\Command;

use MageOS\Installer\Model\Command\IndexerConfigurer;
use MageOS\Installer\Model\Command\ProcessResult;
use MageOS\Installer\Model\Command\ProcessRunner;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Unit tests for IndexerConfigurer
 */
class IndexerConfigurerTest extends TestCase
{
    private ProcessRunner $processRunnerMock;
    private IndexerConfigurer $configurer;
    private BufferedOutput $output;

    protected function setUp(): void
    {
        parent::setUp();
        $this->processRunnerMock = $this->createMock(ProcessRunner::class);
        $this->configurer = new IndexerConfigurer($this->processRunnerMock);
        $this->output = new BufferedOutput();
    }

    public function test_set_schedule_mode_returns_true_on_success(): void
    {
        $successResult = new ProcessResult(true, 'Mode changed');

        $this->processRunnerMock->method('runMagentoCommand')
            ->willReturn($successResult);

        $result = $this->configurer->setScheduleMode('/var/www/magento', $this->output);

        $this->assertTrue($result);
    }

    public function test_set_schedule_mode_returns_false_on_failure(): void
    {
        $failureResult = new ProcessResult(false, '', 'Failed');

        $this->processRunnerMock->method('runMagentoCommand')
            ->willReturn($failureResult);

        $result = $this->configurer->setScheduleMode('/var/www/magento', $this->output);

        $this->assertFalse($result);
    }

    public function test_set_schedule_mode_calls_correct_command(): void
    {
        $successResult = new ProcessResult(true, '');

        $this->processRunnerMock->expects($this->once())
            ->method('runMagentoCommand')
            ->with('indexer:set-mode schedule', $this->anything(), $this->anything())
            ->willReturn($successResult);

        $this->configurer->setScheduleMode('/var/www/magento', $this->output);
    }

    public function test_reindex_all_returns_true_on_success(): void
    {
        $successResult = new ProcessResult(true, 'Reindex complete');

        $this->processRunnerMock->method('runMagentoCommand')
            ->willReturn($successResult);

        $result = $this->configurer->reindexAll('/var/www/magento', $this->output);

        $this->assertTrue($result);
    }

    public function test_reindex_all_uses_longer_timeout(): void
    {
        $successResult = new ProcessResult(true, '');

        $this->processRunnerMock->expects($this->once())
            ->method('runMagentoCommand')
            ->with($this->anything(), $this->anything(), 300)
            ->willReturn($successResult);

        $this->configurer->reindexAll('/var/www/magento', $this->output);
    }
}
