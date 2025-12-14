<?php
/**
 * Copyright © Mage-OS, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Amqp\Test\Unit\Model\QueueConfig;

use Magento\Amqp\Model\QueueConfig\ChangeDetector;
use Magento\Framework\Amqp\Config as AmqpConfig;
use Magento\Framework\MessageQueue\ConnectionTypeResolver;
use Magento\Framework\MessageQueue\Topology\ConfigInterface as TopologyConfigInterface;
use Magento\Framework\MessageQueue\Topology\Config\QueueConfigItemInterface;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Exception\AMQPProtocolChannelException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for ChangeDetector
 */
class ChangeDetectorTest extends TestCase
{
    /**
     * @var AmqpConfig|MockObject
     */
    private AmqpConfig|MockObject $amqpConfig;

    /**
     * @var TopologyConfigInterface|MockObject
     */
    private TopologyConfigInterface|MockObject $topologyConfig;

    /**
     * @var ConnectionTypeResolver|MockObject
     */
    private ConnectionTypeResolver|MockObject $connectionTypeResolver;

    /**
     * @var AMQPChannel|MockObject
     */
    private AMQPChannel|MockObject $channel;

    /**
     * @var ChangeDetector
     */
    private ChangeDetector $changeDetector;

    /**
     * Setup test dependencies
     */
    protected function setUp(): void
    {
        $this->amqpConfig = $this->createMock(AmqpConfig::class);
        $this->topologyConfig = $this->createMock(TopologyConfigInterface::class);
        $this->connectionTypeResolver = $this->createMock(ConnectionTypeResolver::class);
        $this->channel = $this->createMock(AMQPChannel::class);

        $this->changeDetector = new ChangeDetector(
            $this->amqpConfig,
            $this->topologyConfig,
            $this->connectionTypeResolver
        );
    }

    /**
     * Test hasChanges method when no changes are detected
     */
    public function testHasChangesReturnsFalseWhenAllQueuesExist(): void
    {
        $this->setupAmqpQueues(['queue1', 'queue2', 'queue3']);
        $this->setupChannelPassiveChecks(['queue1' => true, 'queue2' => true, 'queue3' => true]);

        $this->assertFalse($this->changeDetector->hasChanges());
    }

    /**
     * Test hasChanges method when changes are detected
     */
    public function testHasChangesReturnsTrueWhenQueuesAreMissing(): void
    {
        $this->setupAmqpQueues(['queue1', 'queue2', 'queue3']);
        $this->setupChannelPassiveChecks(['queue1' => true, 'queue2' => false, 'queue3' => true]);

        $this->assertTrue($this->changeDetector->hasChanges());
    }

    /**
     * Test getMissingQueues method returns correct list
     */
    public function testGetMissingQueuesReturnsCorrectList(): void
    {
        $this->setupAmqpQueues(['queue1', 'queue2', 'queue3', 'queue4']);
        $this->setupChannelPassiveChecks([
            'queue1' => true,
            'queue2' => false,
            'queue3' => true,
            'queue4' => false
        ]);

        $missingQueues = $this->changeDetector->getMissingQueues();

        $this->assertCount(2, $missingQueues);
        $this->assertContains('queue2', $missingQueues);
        $this->assertContains('queue4', $missingQueues);
    }

    /**
     * Test that connection errors propagate
     */
    public function testThrowsExceptionOnAmqpConnectionFailure(): void
    {
        $this->setupAmqpQueues(['queue1']);

        $this->amqpConfig->method('getChannel')
            ->willThrowException(new \Exception('Connection failed'));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Connection failed');

        $this->changeDetector->hasChanges();
    }

    /**
     * Test that non-AMQP queues are ignored
     */
    public function testIgnoresNonAmqpQueues(): void
    {
        $queue1 = $this->createMock(QueueConfigItemInterface::class);
        $queue1->method('getName')->willReturn('amqp.queue');
        $queue1->method('getConnection')->willReturn('amqp');

        $queue2 = $this->createMock(QueueConfigItemInterface::class);
        $queue2->method('getName')->willReturn('db.queue');
        $queue2->method('getConnection')->willReturn('db');

        $this->topologyConfig->method('getQueues')->willReturn([$queue1, $queue2]);

        $this->connectionTypeResolver->method('getConnectionType')
            ->willReturnMap([
                ['amqp', 'amqp'],
                ['db', 'db']
            ]);

        // Only AMQP queue should be checked
        $this->amqpConfig->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->channel->expects($this->once())
            ->method('queue_declare')
            ->with('amqp.queue', true, false, false, false, false)
            ->willReturn(['amqp.queue', 0, 0]);

        $this->assertFalse($this->changeDetector->hasChanges());
    }

    /**
     * Test hasChanges returns false when no queues are configured
     */
    public function testHasChangesReturnsFalseWhenNoQueuesConfigured(): void
    {
        $this->setupAmqpQueues([]);

        $this->assertFalse($this->changeDetector->hasChanges());
        $this->assertEmpty($this->changeDetector->getMissingQueues());
    }

    /**
     * Test that other channel exceptions are propagated
     */
    public function testPropagatesNonNotFoundChannelExceptions(): void
    {
        $this->setupAmqpQueues(['queue1']);

        $this->amqpConfig->method('getChannel')->willReturn($this->channel);

        $exception = new AMQPProtocolChannelException(500, 'Internal error', []);
        $this->channel->method('queue_declare')
            ->willThrowException($exception);

        $this->expectException(AMQPProtocolChannelException::class);
        $this->expectExceptionCode(500);

        $this->changeDetector->hasChanges();
    }

    /**
     * Helper method to setup mock AMQP queues
     *
     * @param array $queueNames
     */
    private function setupAmqpQueues(array $queueNames): void
    {
        $queues = [];
        foreach ($queueNames as $queueName) {
            $queue = $this->createMock(QueueConfigItemInterface::class);
            $queue->method('getName')->willReturn($queueName);
            $queue->method('getConnection')->willReturn('amqp');
            $queues[] = $queue;
        }

        $this->topologyConfig->method('getQueues')->willReturn($queues);
        $this->connectionTypeResolver->method('getConnectionType')->willReturn('amqp');
    }

    /**
     * Helper method to setup channel passive mode checks
     *
     * @param array $queueStatus Map of queue name => exists (bool)
     */
    private function setupChannelPassiveChecks(array $queueStatus): void
    {
        $this->amqpConfig->method('getChannel')->willReturn($this->channel);

        $this->channel->method('queue_declare')
            ->willReturnCallback(function ($queueName, $passive) use ($queueStatus) {
                if (!isset($queueStatus[$queueName])) {
                    return [$queueName, 0, 0];
                }

                if ($queueStatus[$queueName]) {
                    return [$queueName, 0, 0];
                } else {
                    throw new AMQPProtocolChannelException(404, 'NOT_FOUND', []);
                }
            });
    }
}
