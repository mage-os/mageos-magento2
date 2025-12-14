<?php
/**
 * Copyright © Mage-OS, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MysqlMq\Test\Unit\Console;

use Magento\MysqlMq\Console\QueueConfigStatusCommand;
use Magento\MysqlMq\Model\QueueConfig\ChangeDetector;
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
        $this->assertStringContainsString('Queue config files are up to date', $this->tester->getDisplay());
    }

    /**
     * Test execute method when changes are detected
     */
    public function testExecuteReturnsErrorCodeWhenChangesDetected(): void
    {
        $this->changeDetector->method('hasChanges')->willReturn(true);

        $this->tester->execute([]);

        $this->assertEquals(
            QueueConfigStatusCommand::EXIT_CODE_QUEUE_UPDATE_REQUIRED,
            $this->tester->getStatusCode()
        );
        $this->assertStringContainsString('Queue config files have changed', $this->tester->getDisplay());
        $this->assertStringContainsString('setup:upgrade', $this->tester->getDisplay());
    }
}
