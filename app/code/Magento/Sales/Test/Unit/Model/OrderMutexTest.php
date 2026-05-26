<?php
/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Sales\Test\Unit\Model;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Adapter\DeadlockException;
use Magento\Framework\DB\DeadlockRecoveryExecutor;
use Magento\Framework\DB\Select;
use Magento\Sales\Model\OrderMutex;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class OrderMutexTest extends TestCase
{
    /**
     * @var ResourceConnection|MockObject
     */
    private $resourceConnection;

    /**
     * @var AdapterInterface|MockObject
     */
    private $adapterInterface;

    /**
     * @var OrderMutex|MockObject
     */
    private $orderMutex;

    /**
     * @var int
     */
    private $attempts = 5;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resourceConnection = $this->createMock(ResourceConnection::class);
        $this->adapterInterface = $this->createMock(AdapterInterface::class);

        $this->orderMutex = new OrderMutex(
            $this->resourceConnection,
            new DeadlockRecoveryExecutor($this->attempts, 10000)
        );
    }

    public function testExecuteDeadlock()
    {
        $orderId = 1;
        $this->resourceConnection->expects($this->once())->method('getConnection')->with('sales')
            ->willReturn($this->adapterInterface);

        $failedAttempts = array_fill(0, 2, $this->throwException(new DeadlockException()));
        $totalAttempts = [...$failedAttempts, $this->createMock(\Zend_Db_Statement_Interface::class)];
        $totalAttemptsCount = count($totalAttempts);

        $this->mockConnection($orderId, $totalAttemptsCount);

        $this->adapterInterface->expects($this->exactly($totalAttemptsCount))
            ->method('query')
            ->willReturnOnConsecutiveCalls(...$totalAttempts);

        $this->adapterInterface->expects($this->exactly(count($failedAttempts)))
            ->method('rollback');

        $callableNoop = fn () => '7';
        $result = $this->orderMutex->execute($orderId, $callableNoop);

        $this->assertEquals($callableNoop(), $result);
    }

    public function testExecuteDeadlockExhausted()
    {
        $this->expectException(DeadlockException::class);

        $this->resourceConnection->expects($this->once())->method('getConnection')->with('sales')
            ->willReturn($this->adapterInterface);

        $attempts = array_fill(0, $this->attempts, $this->throwException(new DeadlockException()));

        $attemptsCount = count($attempts);

        $orderId = 1;
        $this->mockConnection($orderId, $attemptsCount);

        $this->adapterInterface->expects($this->exactly($attemptsCount))
            ->method('query')
            ->willReturnOnConsecutiveCalls(...$attempts);

        $this->adapterInterface->expects($this->exactly($attemptsCount))
            ->method('rollback');

        $callableNoop = fn () => '7';
        $this->orderMutex->execute($orderId, $callableNoop);
    }

    /**
     * @param int $attemptsCount
     */
    private function mockConnection(int $orderId, int $attemptsCount)
    {
        $select = $this->createMock(Select::class);
        $select->expects($this->exactly($attemptsCount))
            ->method('from')
            ->with('sales_order', 'entity_id')
            ->willReturnSelf();
        $select->expects($this->exactly($attemptsCount))
            ->method('where')
            ->with('entity_id = ?', $orderId)
            ->willReturnSelf();
        $select->expects($this->exactly($attemptsCount))
            ->method('forUpdate')
            ->with(true)
            ->willReturnSelf();
        $this->adapterInterface->expects($this->exactly($attemptsCount))
            ->method('select')
            ->willReturn($select);
        $this->resourceConnection->expects($this->exactly($attemptsCount))
            ->method('getTableName')
            ->willReturnArgument(0);
    }
}
