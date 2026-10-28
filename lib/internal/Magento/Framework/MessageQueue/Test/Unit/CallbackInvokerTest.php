<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\MessageQueue\Test\Unit;

use Magento\Framework\Amqp\Queue as AmqpQueue;
use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\MessageQueue\CallbackInvoker;
use Magento\Framework\MessageQueue\EnvelopeInterface;
use Magento\Framework\MessageQueue\PoisonPill\PoisonPillCompareInterface;
use Magento\Framework\MessageQueue\PoisonPill\PoisonPillReadInterface;
use Magento\Framework\MessageQueue\QueueInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for CallbackInvoker.
 */
class CallbackInvokerTest extends TestCase
{
    /**
     * @var PoisonPillReadInterface|MockObject
     */
    private $poisonPillRead;

    /**
     * @var PoisonPillCompareInterface|MockObject
     */
    private $poisonPillCompare;

    /**
     * @var DeploymentConfig|MockObject
     */
    private $deploymentConfig;

    /**
     * @var CallbackInvoker
     */
    private $invoker;

    protected function setUp(): void
    {
        $this->poisonPillRead = $this->createMock(PoisonPillReadInterface::class);
        $this->poisonPillCompare = $this->createMock(PoisonPillCompareInterface::class);
        $this->deploymentConfig = $this->createMock(DeploymentConfig::class);

        $this->invoker = new CallbackInvoker(
            $this->poisonPillRead,
            $this->poisonPillCompare,
            $this->deploymentConfig
        );
    }

    /**
     * For AMQP queues with a message limit, subscribeWithLimit() must be used instead
     * of the dequeue() polling loop to avoid per-message basic_get round-trips.
     */
    public function testInvokeUsesSubscribeWithLimitForAmqpQueue(): void
    {
        $this->poisonPillRead->expects($this->once())->method('getLatestVersion')->willReturn('v1');
        $this->deploymentConfig->expects($this->once())->method('get')
            ->with('queue/consumers_wait_for_messages', 1)
            ->willReturn(1);

        $queue = $this->createMock(AmqpQueue::class);
        $queue->expects($this->once())
            ->method('subscribeWithLimit')
            ->with($this->isType('callable'), 50, 0);
        $queue->expects($this->never())->method('dequeue');

        $this->invoker->invoke($queue, 50, function () {
        });
    }

    /**
     * When consumers_wait_for_messages=0 the channel wait timeout must be 1 second
     * so the consumer exits promptly once the queue empties.
     */
    public function testInvokePassesWaitTimeoutOneWhenNotWaitingForMessages(): void
    {
        $this->poisonPillRead->method('getLatestVersion')->willReturn('v1');
        $this->deploymentConfig->expects($this->once())->method('get')
            ->with('queue/consumers_wait_for_messages', 1)
            ->willReturn(0);

        $queue = $this->createMock(AmqpQueue::class);
        $queue->expects($this->once())
            ->method('subscribeWithLimit')
            ->with($this->isType('callable'), 10, 1);

        $this->invoker->invoke($queue, 10, function () {
        });
    }

    /**
     * When consumers_wait_for_messages=1 the channel wait timeout must be 0 (block
     * indefinitely) to match the existing behaviour for that setting.
     */
    public function testInvokePassesWaitTimeoutZeroWhenWaitingForMessages(): void
    {
        $this->poisonPillRead->method('getLatestVersion')->willReturn('v1');
        $this->deploymentConfig->expects($this->once())->method('get')
            ->with('queue/consumers_wait_for_messages', 1)
            ->willReturn(1);

        $queue = $this->createMock(AmqpQueue::class);
        $queue->expects($this->once())
            ->method('subscribeWithLimit')
            ->with($this->isType('callable'), 10, 0);

        $this->invoker->invoke($queue, 10, function () {
        });
    }

    /**
     * Non-AMQP queues (e.g. MySQL-backed) must keep using the original dequeue() loop.
     */
    public function testInvokeFallsBackToDequeueLoopForNonAmqpQueue(): void
    {
        $this->poisonPillRead->method('getLatestVersion')->willReturn('v1');
        $this->poisonPillCompare->method('isLatestVersion')->willReturn(true);
        $this->deploymentConfig->method('get')
            ->with('queue/consumers_wait_for_messages', 1)
            ->willReturn(0);

        $queue = $this->createMock(QueueInterface::class);
        $queue->expects($this->once())->method('dequeue')->willReturn(null);

        $invoked = false;
        $this->invoker->invoke($queue, 1, function () use (&$invoked) {
            $invoked = true;
        });

        $this->assertFalse($invoked, 'Callback must not be called when dequeue returns null');
    }

    /**
     * The wrapped callback passed to subscribeWithLimit must delegate to the original
     * callback when the poison pill version is current.
     */
    public function testInvokeWrappedCallbackDelegatesToOriginalCallback(): void
    {
        $this->poisonPillRead->method('getLatestVersion')->willReturn('v1');
        $this->poisonPillCompare->method('isLatestVersion')->willReturn(true);
        $this->deploymentConfig->method('get')
            ->with('queue/consumers_wait_for_messages', 1)
            ->willReturn(1);

        $capturedCallback = null;
        $queue = $this->createMock(AmqpQueue::class);
        $queue->expects($this->once())
            ->method('subscribeWithLimit')
            ->willReturnCallback(function (callable $callback) use (&$capturedCallback) {
                $capturedCallback = $callback;
            });

        $originalCalled = false;
        $originalCallback = function () use (&$originalCalled) {
            $originalCalled = true;
        };

        $this->invoker->invoke($queue, 5, $originalCallback);

        $this->assertIsCallable($capturedCallback);
        $envelope = $this->createMock(EnvelopeInterface::class);
        $capturedCallback($envelope);

        $this->assertTrue($originalCalled, 'Original callback must be invoked when poison pill is current');
    }
}
