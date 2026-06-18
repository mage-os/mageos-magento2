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
    /** @var ProcessRunner */
    private ProcessRunner $processRunnerMock;
    /** @var IndexerConfigurer */
    private IndexerConfigurer $configurer;
    /** @var BufferedOutput */
    private BufferedOutput $output;

    protected function setUp(): void
    {
        parent::setUp();
        $this->processRunnerMock = $this->createMock(ProcessRunner::class);
        $this->configurer = new IndexerConfigurer($this->processRunnerMock);
        $this->output = new BufferedOutput();
    }

    public function testSetScheduleModeReturnsTrueOnSuccess(): void
    {
        $successResult = new ProcessResult(true, 'Mode changed');

        $this->processRunnerMock->method('runMagentoCommand')
            ->willReturn($successResult);

        $result = $this->configurer->setScheduleMode('/var/www/magento', $this->output);

        $this->assertTrue($result);
    }

    public function testSetScheduleModeReturnsFalseOnFailure(): void
    {
        $failureResult = new ProcessResult(false, '', 'Failed');

        $this->processRunnerMock->method('runMagentoCommand')
            ->willReturn($failureResult);

        $result = $this->configurer->setScheduleMode('/var/www/magento', $this->output);

        $this->assertFalse($result);
    }

    public function testSetScheduleModeCallsCorrectCommand(): void
    {
        $successResult = new ProcessResult(true, '');

        $this->processRunnerMock->expects($this->once())
            ->method('runMagentoCommand')
            ->with(['indexer:set-mode', 'schedule'], $this->anything(), $this->anything())
            ->willReturn($successResult);

        $this->configurer->setScheduleMode('/var/www/magento', $this->output);
    }

    public function testReindexAllReturnsTrueOnSuccess(): void
    {
        $successResult = new ProcessResult(true, 'Reindex complete');

        $this->processRunnerMock->method('runMagentoCommand')
            ->willReturn($successResult);

        $result = $this->configurer->reindexAll('/var/www/magento', $this->output);

        $this->assertTrue($result);
    }

    public function testReindexAllUsesLongerTimeout(): void
    {
        $successResult = new ProcessResult(true, '');

        $this->processRunnerMock->expects($this->once())
            ->method('runMagentoCommand')
            ->with($this->anything(), $this->anything(), 300)
            ->willReturn($successResult);

        $this->configurer->reindexAll('/var/www/magento', $this->output);
    }
}
