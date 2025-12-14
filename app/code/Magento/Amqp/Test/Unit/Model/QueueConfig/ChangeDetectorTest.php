<?php
/**
 * Copyright © Mage-OS, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Amqp\Test\Unit\Model\QueueConfig;

use Exception;
use Magento\Amqp\Model\QueueConfig\ChangeDetector;
use Magento\Framework\Amqp\Config as AmqpConfig;
use Magento\Framework\MessageQueue\ConnectionTypeResolver;
use Magento\Framework\MessageQueue\Topology\Config\CompositeReader;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Exception\AMQPProtocolChannelException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Test for AMQP ChangeDetector
 */
class ChangeDetectorTest extends TestCase
{
    /**
     * @var AmqpConfig|MockObject
     */
    private AmqpConfig|MockObject $amqpConfig;

    /**
     * @var CompositeReader|MockObject
     */
    private CompositeReader|MockObject $topologyConfigReader;

    /**
     * @var ConnectionTypeResolver|MockObject
     */
    private ConnectionTypeResolver|MockObject $connectionTypeResolver;

    /**
     * @var AMQPChannel|MockObject
     */
    private AMQPChannel|MockObject $channel;

    /**
     * @var LoggerInterface|MockObject
     */
    private LoggerInterface|MockObject $logger;

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
        $this->topologyConfigReader = $this->createMock(CompositeReader::class);
        $this->connectionTypeResolver = $this->createMock(ConnectionTypeResolver::class);
        $this->channel = $this->createMock(AMQPChannel::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->changeDetector = new ChangeDetector(
            $this->amqpConfig,
            $this->topologyConfigReader,
            $this->connectionTypeResolver,
            $this->logger
        );
    }

    /**
     * Test hasChanges returns false when all queues exist
     */
    public function testHasChangesReturnsFalseWhenAllQueuesExist(): void
    {
        $this->setupAmqpQueues(['queue1', 'queue2']);
        $this->setupChannelExpectations(['queue1' => true, 'queue2' => true]);

        $this->assertFalse($this->changeDetector->hasChanges());
    }

    /**
     * Test hasChanges returns true when queues are missing
     */
    public function testHasChangesReturnsTrueWhenQueuesAreMissing(): void
    {
        $this->setupAmqpQueues(['queue1', 'queue2', 'queue3']);
        $this->setupChannelExpectations([
            'queue1' => true,
            'queue2' => false,
            'queue3' => true
        ]);

        $this->assertTrue($this->changeDetector->hasChanges());
    }

    /**
     * Test hasChanges returns false when no AMQP queues configured
     */
    public function testHasChangesReturnsFalseWhenNoAmqpQueues(): void
    {
        $this->setupAmqpQueues([]);

        $this->assertFalse($this->changeDetector->hasChanges());
    }

    /**
     * Test hasChanges returns false when AMQP is not configured (LogicException)
     */
    public function testHasChangesReturnsFalseWhenConnectionFails(): void
    {
        $this->setupAmqpQueues(['queue1']);
        $this->amqpConfig->method('getChannel')
            ->willThrowException(new \LogicException('Unknown connection name amqp'));

        $this->logger->expects($this->once())
            ->method('info')
            ->with($this->stringContains('AMQP queue status check skipped'));

        $this->assertFalse($this->changeDetector->hasChanges());
    }

    /**
     * Test hasChanges returns false on unexpected exception
     */
    public function testHasChangesReturnsFalseOnUnexpectedException(): void
    {
        $this->setupAmqpQueues(['queue1']);
        $this->amqpConfig->method('getChannel')
            ->willThrowException(new \RuntimeException('Connection timeout'));

        $this->logger->expects($this->once())
            ->method('warning')
            ->with($this->stringContains('Failed to check AMQP queue status'));

        $this->assertFalse($this->changeDetector->hasChanges());
    }

    /**
     * Test getMissingQueues returns correct list
     */
    public function testGetMissingQueuesReturnsCorrectList(): void
    {
        $this->setupAmqpQueues(['queue1', 'queue2', 'queue3', 'queue4']);
        $this->setupChannelExpectations([
            'queue1' => true,
            'queue2' => false,
            'queue3' => true,
            'queue4' => false
        ]);

        $missingQueues = $this->changeDetector->getMissingQueues();

        $this->assertEquals(['queue2', 'queue4'], $missingQueues);
    }

    /**
     * Test only AMQP queues are checked
     */
    public function testOnlyAmqpQueuesAreChecked(): void
    {
        $config = [
            'amqpExchange' => [
                'connection' => 'amqp',  // Exchange-level AMQP
                'bindings' => [
                    ['destination' => 'queue1', 'destinationType' => 'queue'],
                    ['destination' => 'queue3', 'destinationType' => 'queue'],
                ]
            ],
            'dbExchange' => [
                'connection' => 'db',  // Exchange-level DB
                'bindings' => [
                    ['destination' => 'queue2', 'destinationType' => 'queue'],
                ]
            ]
        ];

        $this->topologyConfigReader->method('read')->willReturn($config);

        $this->connectionTypeResolver->method('getConnectionType')
            ->willReturnCallback(function ($connection) {
                return $connection === 'amqp' ? 'amqp' : 'db';
            });

        $this->setupChannelExpectations(['queue1' => true, 'queue3' => true]);

        $this->assertFalse($this->changeDetector->hasChanges());
    }

    /**
     * Helper method to setup AMQP queues
     *
     * @param array $queueNames
     */
    private function setupAmqpQueues(array $queueNames): void
    {
        $config = [];
        foreach ($queueNames as $queueName) {
            $config['exchange_' . $queueName] = [
                'connection' => 'amqp',  // Exchange-level connection
                'bindings' => [
                    [
                        'destination' => $queueName,
                        'destinationType' => 'queue'
                    ]
                ]
            ];
        }

        $this->topologyConfigReader->method('read')->willReturn($config);
        $this->connectionTypeResolver->method('getConnectionType')
            ->with('amqp')
            ->willReturn('amqp');
    }

    /**
     * Helper method to setup channel expectations
     *
     * @param array $queues Array of queue name => exists mappings
     */
    private function setupChannelExpectations(array $queues): void
    {
        $this->amqpConfig->method('getChannel')->willReturn($this->channel);

        $callCount = 0;
        $this->channel->expects($this->exactly(count($queues)))
            ->method('queue_declare')
            ->willReturnCallback(function ($queueName) use ($queues, &$callCount) {
                $callCount++;
                if (!$queues[$queueName]) {
                    $exception = new AMQPProtocolChannelException(404, 'NOT_FOUND', [0, 0]);
                    throw $exception;
                }
                return null;
            });

        // Mock channel close for 404 errors
        $this->channel->method('close')->willReturn(null);
    }
}
