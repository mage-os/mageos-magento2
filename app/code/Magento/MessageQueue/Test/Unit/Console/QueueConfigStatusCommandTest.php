<?php
/**
 * Copyright © Mage-OS, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MessageQueue\Test\Unit\Console;

use Magento\MessageQueue\Console\QueueConfigStatusCommand;
use Magento\MessageQueue\Model\QueueConfig\ChangeDetectorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Test for QueueConfigStatusCommand
 */
class QueueConfigStatusCommandTest extends TestCase
{
    /**
     * @var ChangeDetectorInterface|MockObject
     */
    private ChangeDetectorInterface|MockObject $changeDetector1;

    /**
     * @var ChangeDetectorInterface|MockObject
     */
    private ChangeDetectorInterface|MockObject $changeDetector2;

    /**
     * @var CommandTester
     */
    private CommandTester $tester;

    /**
     * Setup test dependencies
     */
    protected function setUp(): void
    {
        $this->changeDetector1 = $this->createMock(ChangeDetectorInterface::class);
        $this->changeDetector2 = $this->createMock(ChangeDetectorInterface::class);
    }

    /**
     * Test execute method when no changes are detected
     */
    public function testExecuteReturnsSuccessWhenNoChanges(): void
    {
        $this->changeDetector1->method('hasChanges')->willReturn(false);
        $this->changeDetector2->method('hasChanges')->willReturn(false);

        $command = new QueueConfigStatusCommand([
            'detector1' => $this->changeDetector1,
            'detector2' => $this->changeDetector2
        ]);
        $this->tester = new CommandTester($command);

        $this->tester->execute([]);

        $this->assertEquals(0, $this->tester->getStatusCode());
        $this->assertStringContainsString('Queue config files are up to date', $this->tester->getDisplay());
    }

    /**
     * Test execute method when changes are detected by first detector
     */
    public function testExecuteReturnsErrorCodeWhenFirstDetectorHasChanges(): void
    {
        $this->changeDetector1->method('hasChanges')->willReturn(true);
        $this->changeDetector2->method('hasChanges')->willReturn(false);

        $command = new QueueConfigStatusCommand([
            'detector1' => $this->changeDetector1,
            'detector2' => $this->changeDetector2
        ]);
        $this->tester = new CommandTester($command);

        $this->tester->execute([]);

        $this->assertEquals(
            QueueConfigStatusCommand::EXIT_CODE_QUEUE_UPDATE_REQUIRED,
            $this->tester->getStatusCode()
        );
        $this->assertStringContainsString('Queue config files have changed', $this->tester->getDisplay());
        $this->assertStringContainsString('setup:upgrade', $this->tester->getDisplay());
    }

    /**
     * Test execute method when changes are detected by second detector
     */
    public function testExecuteReturnsErrorCodeWhenSecondDetectorHasChanges(): void
    {
        $this->changeDetector1->method('hasChanges')->willReturn(false);
        $this->changeDetector2->method('hasChanges')->willReturn(true);

        $command = new QueueConfigStatusCommand([
            'detector1' => $this->changeDetector1,
            'detector2' => $this->changeDetector2
        ]);
        $this->tester = new CommandTester($command);

        $this->tester->execute([]);

        $this->assertEquals(
            QueueConfigStatusCommand::EXIT_CODE_QUEUE_UPDATE_REQUIRED,
            $this->tester->getStatusCode()
        );
        $this->assertStringContainsString('Queue config files have changed', $this->tester->getDisplay());
        $this->assertStringContainsString('setup:upgrade', $this->tester->getDisplay());
    }

    /**
     * Test execute method with no detectors provided
     */
    public function testExecuteReturnsSuccessWhenNoDetectors(): void
    {
        $command = new QueueConfigStatusCommand([]);
        $this->tester = new CommandTester($command);

        $this->tester->execute([]);

        $this->assertEquals(0, $this->tester->getStatusCode());
        $this->assertStringContainsString('Queue config files are up to date', $this->tester->getDisplay());
    }

    /**
     * Test execute method with single detector
     */
    public function testExecuteWorksWithSingleDetector(): void
    {
        $this->changeDetector1->method('hasChanges')->willReturn(false);

        $command = new QueueConfigStatusCommand([
            'detector1' => $this->changeDetector1
        ]);
        $this->tester = new CommandTester($command);

        $this->tester->execute([]);

        $this->assertEquals(0, $this->tester->getStatusCode());
        $this->assertStringContainsString('Queue config files are up to date', $this->tester->getDisplay());
    }
}
