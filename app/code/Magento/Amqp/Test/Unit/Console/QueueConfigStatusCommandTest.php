<?php
/**
 * Copyright © Mage-OS, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Amqp\Test\Unit\Console;

use Magento\Amqp\Console\QueueConfigStatusCommand;
use Magento\Amqp\Model\QueueConfig\ChangeDetector;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Test for QueueConfigStatusCommand
 */
class QueueConfigStatusCommandTest extends TestCase
{
    /**
     * @var ChangeDetector|MockObject
     */
    private ChangeDetector|MockObject $changeDetector;

    /**
     * @var CommandTester
     */
    private CommandTester $tester;

    /**
     * Setup test dependencies
     */
    protected function setUp(): void
    {
        $this->changeDetector = $this->createMock(ChangeDetector::class);

        $command = new QueueConfigStatusCommand($this->changeDetector);
        $this->tester = new CommandTester($command);
    }

    /**
     * Test execute method when no changes are detected
     */
    public function testExecuteReturnsSuccessWhenNoChanges(): void
    {
        $this->changeDetector->method('hasChanges')->willReturn(false);

        $this->tester->execute([]);

        $this->assertEquals(0, $this->tester->getStatusCode());
        $this->assertStringContainsString('AMQP queue config is up to date', $this->tester->getDisplay());
    }

    /**
     * Test execute method when changes are detected
     */
    public function testExecuteReturnsErrorCodeWhenQueuesAreMissing(): void
    {
        $this->changeDetector->method('hasChanges')->willReturn(true);
        $this->changeDetector->method('getMissingQueues')->willReturn(['queue1', 'queue2']);

        $this->tester->execute([]);

        $this->assertEquals(
            QueueConfigStatusCommand::EXIT_CODE_QUEUE_UPDATE_REQUIRED,
            $this->tester->getStatusCode()
        );
        $this->assertStringContainsString('AMQP queues missing: queue1, queue2', $this->tester->getDisplay());
        $this->assertStringContainsString('setup:upgrade', $this->tester->getDisplay());
    }

    /**
     * Test execute method when connection fails
     */
    public function testExecuteReturnsErrorOnConnectionFailure(): void
    {
        $this->changeDetector->method('hasChanges')
            ->willThrowException(new \Exception('Connection timeout'));

        $this->tester->execute([]);

        $this->assertEquals(1, $this->tester->getStatusCode());
        $this->assertStringContainsString('Cannot connect to AMQP broker', $this->tester->getDisplay());
        $this->assertStringContainsString('Connection timeout', $this->tester->getDisplay());
    }

    /**
     * Test execute method when AMQP is not configured
     */
    public function testExecuteReturnsSuccessWhenAmqpNotConfigured(): void
    {
        $this->changeDetector->method('hasChanges')
            ->willThrowException(new \LogicException('Unknown connection name amqp'));

        $this->tester->execute([]);

        $this->assertEquals(0, $this->tester->getStatusCode());
        $this->assertStringContainsString('AMQP not configured', $this->tester->getDisplay());
    }

    /**
     * Test execute method with runtime exception
     */
    public function testExecuteReturnsErrorOnException(): void
    {
        $this->changeDetector->method('hasChanges')
            ->willThrowException(new \RuntimeException('Unexpected error'));

        $this->tester->execute([]);

        $this->assertEquals(1, $this->tester->getStatusCode());
        $this->assertStringContainsString('Cannot connect to AMQP broker', $this->tester->getDisplay());
        $this->assertStringContainsString('Unexpected error', $this->tester->getDisplay());
    }
}
