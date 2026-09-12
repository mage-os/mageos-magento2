<?php
/**
 * Copyright 2021 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Amqp\Test\Unit;

use Magento\Framework\Amqp\Config;
use Magento\Framework\Amqp\Queue;
use Magento\Framework\MessageQueue\ConnectionLostException;
use Magento\Framework\MessageQueue\EnvelopeFactory;
use Magento\Framework\MessageQueue\EnvelopeInterface;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Exception\AMQPTimeoutException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class QueueTest extends TestCase
{
    private const PREFETCH_COUNT = 100;
    /**
     * @var Config|MockObject
     */
    private $config;

    /**
     * @var EnvelopeFactory|MockObject
     */
    private $envelopeFactory;

    /**
     * @var LoggerInterface|MockObject
     */
    private $logger;

    /**
     * @var Queue
     */
    private $model;

    protected function setUp(): void
    {
        $this->config = $this->createMock(Config::class);
        $this->envelopeFactory = $this->createMock(EnvelopeFactory::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->model = new Queue(
            $this->config,
            $this->envelopeFactory,
            'testQueue',
            $this->logger,
            self::PREFETCH_COUNT
        );
    }

    /**
     * Test verifies that prefetch value is used to specify how many messages
     * are being sent to the consumer at the same time.
     */
    public function testSubscribe()
    {
        $callback = function () {
        };
        $amqpChannel = $this->createMock(AMQPChannel::class);
        $amqpChannel->expects($this->once())
            ->method('basic_qos')
            ->with(0, self::PREFETCH_COUNT, false);
        $this->config->expects($this->once())
            ->method('getChannel')
            ->willReturn($amqpChannel);

        $this->model->subscribe($callback);
    }

    public function testAcknowledgeThrowsExceptionWhenChannelChanged(): void
    {
        $deliveryChannel = $this->createMock(AMQPChannel::class);
        $currentChannel = $this->createMock(AMQPChannel::class);
        $envelope = $this->createMock(EnvelopeInterface::class);
        $properties = [
            'delivery_tag' => 123,
            'delivery_channel' => $deliveryChannel
        ];

        $this->config->expects($this->once())
            ->method('getChannel')
            ->willReturn($currentChannel);
        $currentChannel->expects($this->never())
            ->method('basic_ack');
        $envelope->expects($this->once())
            ->method('getProperties')
            ->willReturn($properties);

        $this->expectException(ConnectionLostException::class);
        $this->expectExceptionMessage('skipping ack');

        $this->model->acknowledge($envelope);
    }

    public function testRejectThrowsExceptionWhenChannelChanged(): void
    {
        $deliveryChannel = $this->createMock(AMQPChannel::class);
        $currentChannel = $this->createMock(AMQPChannel::class);
        $envelope = $this->createMock(EnvelopeInterface::class);
        $properties = [
            'delivery_tag' => 123,
            'delivery_channel' => $deliveryChannel
        ];

        $this->config->expects($this->once())
            ->method('getChannel')
            ->willReturn($currentChannel);
        $currentChannel->expects($this->never())
            ->method('basic_reject');
        $envelope->expects($this->once())
            ->method('getProperties')
            ->willReturn($properties);

        $this->expectException(ConnectionLostException::class);
        $this->expectExceptionMessage('skipping reject');

        $this->model->reject($envelope);
    }

    /**
     * Test verifies subscribeWithLimit sets up basic_qos and basic_consume correctly.
     */
    public function testSubscribeWithLimitCallsBasicQosAndBasicConsume(): void
    {
        $amqpChannel = $this->createMock(AMQPChannel::class);
        $amqpChannel->expects($this->once())
            ->method('basic_qos')
            ->with(0, self::PREFETCH_COUNT, false);
        $amqpChannel->expects($this->once())
            ->method('basic_consume')
            ->with('testQueue', '', false, false, false, false, $this->isType('callable'))
            ->willReturn('test-consumer-tag');
        // callbacks is empty by default so the while loop exits immediately
        $this->config->expects($this->once())
            ->method('getChannel')
            ->willReturn($amqpChannel);

        $this->model->subscribeWithLimit(function () {
        }, 10);
    }

    /**
     * Test verifies subscribeWithLimit does nothing when maxMessages is zero or negative,
     * matching the original dequeue() loop behaviour of for ($i = 0; $i > 0; ...).
     */
    public function testSubscribeWithLimitDoesNothingWhenMaxMessagesIsZero(): void
    {
        // getChannel must never be called — no AMQP interaction should occur.
        $this->config->expects($this->never())->method('getChannel');

        $this->model->subscribeWithLimit(function () {
        }, 0);
    }

    /**
     * Test verifies subscribeWithLimit exits cleanly when AMQPTimeoutException is thrown,
     * meaning the queue drained before $maxMessages were processed.
     */
    public function testSubscribeWithLimitExitsCleanlyOnAMQPTimeout(): void
    {
        $amqpChannel = $this->createMock(AMQPChannel::class);
        $amqpChannel->method('basic_qos');
        $amqpChannel->method('basic_consume')
            ->willReturnCallback(function () use ($amqpChannel) {
                $amqpChannel->callbacks = ['test-consumer-tag' => function () {
                }];
                return 'test-consumer-tag';
            });
        $amqpChannel->expects($this->once())
            ->method('wait')
            ->willThrowException(new AMQPTimeoutException());
        $this->config->method('getChannel')->willReturn($amqpChannel);

        // Must not throw; AMQPTimeoutException signals empty queue, not a failure.
        $this->model->subscribeWithLimit(function () {
        }, 10, 1);
    }

    /**
     * Test verifies a waitTimeout of 0 ("block indefinitely", used when
     * consumers_wait_for_messages=1) is forwarded to $channel->wait() as the
     * integer 0 and never as null. php-amqplib maps a null wait timeout onto
     * stream_select() with a zero timeval — a non-blocking poll — which turns an
     * idle consumer into a 100% CPU busy-loop. Passing 0 blocks until a frame
     * arrives, matching the sibling subscribe() method.
     */
    public function testSubscribeWithLimitPassesZeroTimeoutSoWaitBlocks(): void
    {
        $amqpChannel = $this->createMock(AMQPChannel::class);
        $amqpChannel->method('basic_qos');
        $amqpChannel->method('basic_consume')
            ->willReturnCallback(function () use ($amqpChannel) {
                $amqpChannel->callbacks = ['test-consumer-tag' => function () {
                }];
                return 'test-consumer-tag';
            });
        // identicalTo enforces === so a null timeout fails the expectation
        // (with()'s default equalTo would let null pass, since null == 0 in PHP).
        $amqpChannel->expects($this->once())
            ->method('wait')
            ->with(null, false, $this->identicalTo(0))
            ->willReturnCallback(function () use ($amqpChannel) {
                // Simulate basic_cancel emptying callbacks so the while loop ends.
                $amqpChannel->callbacks = [];
            });
        $this->config->method('getChannel')->willReturn($amqpChannel);

        $this->model->subscribeWithLimit(function () {
        }, 10, 0);
    }
}
