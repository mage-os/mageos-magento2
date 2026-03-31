<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Framework\Amqp\Test\Unit;

use Magento\Framework\Amqp\Config;
use Magento\Framework\Amqp\Queue;
use Magento\Framework\MessageQueue\EnvelopeFactory;
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

        $this->model->subscribeWithLimit(function () {}, 10);
    }

    /**
     * Test verifies subscribeWithLimit does nothing when maxMessages is zero or negative,
     * matching the original dequeue() loop behaviour of for ($i = 0; $i > 0; ...).
     */
    public function testSubscribeWithLimitDoesNothingWhenMaxMessagesIsZero(): void
    {
        // getChannel must never be called — no AMQP interaction should occur.
        $this->config->expects($this->never())->method('getChannel');

        $this->model->subscribeWithLimit(function () {}, 0);
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
                $amqpChannel->callbacks = ['test-consumer-tag' => function () {}];
                return 'test-consumer-tag';
            });
        $amqpChannel->expects($this->once())
            ->method('wait')
            ->willThrowException(new AMQPTimeoutException());
        $this->config->method('getChannel')->willReturn($amqpChannel);

        // Must not throw; AMQPTimeoutException signals empty queue, not a failure.
        $this->model->subscribeWithLimit(function () {}, 10, 1);
    }
}
