<?php
/**
 * Copyright 2020 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Indexer\Test\Unit\Model;

use Magento\Framework\Amqp\ConfigPool as AmqpConfigPool;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Registry;
use Magento\Indexer\Model\ProcessManager;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Class covers process manager execution test logic
 *
 * @requires function pcntl_fork
 * @see \Magento\Indexer\Model\ProcessManager::isCanBeParalleled
 */
class ProcessManagerTest extends TestCase
{
    /**
     * @dataProvider functionsWithErrorProvider
     * @param array $userFunctions
     * @param int $threadsCount
     * @return void
     */
    public function testFailureInChildProcessHandleMultiThread(array $userFunctions, int $threadsCount): void
    {
        $connectionMock = $this->createMock(ResourceConnection::class);
        $registryMock = $this->createMock(Registry::class);
        $loggerMock = $this->createMock(LoggerInterface::class);
        $amqpConfigPoolMock = $this->createMock(AmqpConfigPool::class);
        $processManager = new ProcessManager(
            $connectionMock,
            $registryMock,
            $threadsCount,
            $loggerMock,
            $amqpConfigPoolMock
        );

        $connectionMock->expects($this->once())
            ->method('closeConnection');
        $amqpConfigPoolMock->expects($this->once())
            ->method('closeConnections');

        try {
            $processManager->execute($userFunctions);
            $this->fail('Exception was not handled');
        } catch (\RuntimeException $exception) {
            $this->assertEquals('Fail in child process', $exception->getMessage());
        }
    }

    /**
     * Closure functions data provider for multi thread execution
     *
     * @return array
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    public static function functionsWithErrorProvider(): array
    {
        return [
            'more_threads_than_functions' => [
                'userFunctions' => [
                    // @codingStandardsIgnoreStart
                    function () {
                        exit(1);
                    },
                    function () {
                        exit(0);
                    },
                    function () {
                        exit(0);
                    },
                    // @codingStandardsIgnoreEnd
                ],
                'threadsCount' => 4,
            ],
            'less_threads_than_functions' => [
                'userFunctions' => [
                    // @codingStandardsIgnoreStart
                    function () {
                        exit(1);
                    },
                    function () {
                        exit(0);
                    },
                    function () {
                        exit(0);
                    },
                    // @codingStandardsIgnoreEnd
                ],
                'threadsCount' => 2,
            ],
            'equal_threads_and_functions' => [
                'userFunctions' => [
                    // @codingStandardsIgnoreStart
                    function () {
                        exit(1);
                    },
                    function () {
                        exit(0);
                    },
                    function () {
                        exit(0);
                    },
                    // @codingStandardsIgnoreEnd
                ],
                'threadsCount' => 3,
            ],
        ];
    }

    /**
     * @dataProvider successFunctionsProvider
     * @param array $userFunctions
     * @param int $threadsCount
     * @return void
     */
    public function testSuccessChildProcessHandleMultiThread(array $userFunctions, int $threadsCount): void
    {
        $connectionMock = $this->createMock(ResourceConnection::class);
        $registryMock = $this->createMock(Registry::class);
        $loggerMock = $this->createMock(LoggerInterface::class);
        $amqpConfigPoolMock = $this->createMock(AmqpConfigPool::class);
        $processManager = new ProcessManager(
            $connectionMock,
            $registryMock,
            $threadsCount,
            $loggerMock,
            $amqpConfigPoolMock
        );

        $connectionMock->expects($this->once())
            ->method('closeConnection');
        $amqpConfigPoolMock->expects($this->once())
            ->method('closeConnections');

        try {
            $processManager->execute($userFunctions);
        } catch (\RuntimeException $exception) {
            $this->fail('Exception was not handled');
        }
    }

    /**
     * Closure functions data provider for multi thread execution
     *
     * @return array
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    public static function successFunctionsProvider(): array
    {
        return [
            'more_threads_than_functions' => [
                'userFunctions' => [
                    // @codingStandardsIgnoreStart
                    function () {
                        exit(0);
                    },
                    function () {
                        exit(0);
                    },
                    function () {
                        exit(0);
                    },
                    // @codingStandardsIgnoreEnd
                ],
                'threadsCount' => 4,
            ],
            'less_threads_than_functions' => [
                'userFunctions' => [
                    // @codingStandardsIgnoreStart
                    function () {
                        exit(0);
                    },
                    function () {
                        exit(0);
                    },
                    function () {
                        exit(0);
                    },
                    // @codingStandardsIgnoreEnd
                ],
                'threadsCount' => 2,
            ],
            'equal_threads_and_functions' => [
                'userFunctions' => [
                    // @codingStandardsIgnoreStart
                    function () {
                        exit(0);
                    },
                    function () {
                        exit(0);
                    },
                    function () {
                        exit(0);
                    },
                    // @codingStandardsIgnoreEnd
                ],
                'threadsCount' => 3,
            ],
        ];
    }

    /**
     * @dataProvider testIsMultiThreadsExecuteDataProvider
     * @param $threadsCount
     * @param $expectedResult
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testIsMultiThreadsExecute($threadsCount, $expectedResult): void
    {
        $connectionMock = $this->createMock(ResourceConnection::class);
        $registryMock = $this->createMock(Registry::class);
        $loggerMock = $this->createMock(LoggerInterface::class);
        $amqpConfigPoolMock = $this->createMock(AmqpConfigPool::class);
        $processManager = new ProcessManager(
            $connectionMock,
            $registryMock,
            $threadsCount,
            $loggerMock,
            $amqpConfigPoolMock
        );
        $this->assertEquals($expectedResult, $processManager->isMultiThreadsExecute());
    }

    public static function testIsMultiThreadsExecuteDataProvider(): array
    {
        return [
            'threadsCount is null' => [null, false],
            'threadsCount is 0' => [0, false],
            'threadsCount is 1' => [1, false],
            'threadsCount is 2' => [2, true],
        ];
    }
}
