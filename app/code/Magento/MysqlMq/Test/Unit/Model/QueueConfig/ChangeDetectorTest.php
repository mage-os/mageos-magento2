<?php
/**
 * Copyright © Mage-OS, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MysqlMq\Test\Unit\Model\QueueConfig;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\MessageQueue\Topology\Config\CompositeReader;
use Magento\MysqlMq\Model\QueueConfig\ChangeDetector;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for ChangeDetector
 */
class ChangeDetectorTest extends TestCase
{
    /**
     * @var CompositeReader|MockObject
     */
    private CompositeReader|MockObject $topologyConfigReader;

    /**
     * @var ResourceConnection|MockObject
     */
    private ResourceConnection|MockObject $resourceConnection;

    /**
     * @var AdapterInterface|MockObject
     */
    private AdapterInterface|MockObject $connection;

    /**
     * @var ChangeDetector
     */
    private ChangeDetector $changeDetector;

    /**
     * Setup test dependencies
     */
    protected function setUp(): void
    {
        $this->topologyConfigReader = $this->createMock(CompositeReader::class);
        $this->resourceConnection = $this->createMock(ResourceConnection::class);
        $this->connection = $this->createMock(AdapterInterface::class);

        $this->resourceConnection->method('getConnection')->willReturn($this->connection);

        $this->changeDetector = new ChangeDetector(
            $this->topologyConfigReader,
            $this->resourceConnection
        );
    }

    /**
     * Test hasChanges method when no changes are detected
     */
    public function testHasChangesReturnsFalseWhenQueuesMatch(): void
    {
        $this->setupDatabaseQueues(['queue1', 'queue2', 'queue3']);
        $this->setupConfigQueues(['queue1', 'queue2', 'queue3']);

        $this->assertFalse($this->changeDetector->hasChanges());
    }

    /**
     * Test hasChanges method when changes are detected
     */
    public function testHasChangesReturnsTrueWhenNewQueuesInConfig(): void
    {
        $this->setupDatabaseQueues(['queue1', 'queue2']);
        $this->setupConfigQueues(['queue1', 'queue2', 'queue3']);

        $this->assertTrue($this->changeDetector->hasChanges());
    }

    /**
     * Test hasChanges method when database has extra queues
     */
    public function testHasChangesReturnsFalseWhenDatabaseHasExtraQueues(): void
    {
        // Orphaned database queues should be ignored
        $this->setupDatabaseQueues(['queue1', 'queue2', 'deleted.queue']);
        $this->setupConfigQueues(['queue1', 'queue2']);

        $this->assertFalse($this->changeDetector->hasChanges());
    }

    /**
     * Test hasChanges method when multiple queues are missing in database
     */
    public function testHasChangesReturnsTrueWhenMultipleQueuesAreMissing(): void
    {
        $this->setupDatabaseQueues(['queue1']);
        $this->setupConfigQueues(['queue1', 'queue2', 'queue3', 'queue4']);

        $this->assertTrue($this->changeDetector->hasChanges());
    }

    /**
     * Test hasChanges method when no queues are configured
     */
    public function testHasChangesReturnsFalseWhenNoQueuesConfigured(): void
    {
        $this->setupDatabaseQueues([]);
        $this->setupConfigQueues([]);

        $this->assertFalse($this->changeDetector->hasChanges());
    }

    /**
     * Test hasChanges method when no queues are in database
     */
    public function testHasChangesHandlesDuplicateQueuesInConfig(): void
    {
        $this->setupDatabaseQueues(['queue1']);
        $this->setupConfigQueuesRaw([
            'exchange1' => [
                'bindings' => [
                    ['destination' => 'queue1', 'destinationType' => 'queue'],
                    ['destination' => 'queue1', 'destinationType' => 'queue'], // duplicate
                ]
            ]
        ]);

        $this->assertFalse($this->changeDetector->hasChanges());
    }

    /**
     * Test hasChanges method ignores non-queue bindings
     */
    public function testHasChangesIgnoresNonQueueBindings(): void
    {
        $this->setupDatabaseQueues(['queue1']);
        $this->setupConfigQueuesRaw([
            'exchange1' => [
                'bindings' => [
                    ['destination' => 'queue1', 'destinationType' => 'queue'],
                    ['destination' => 'topic1', 'destinationType' => 'topic'],
                    ['destination' => 'exchange2', 'destinationType' => 'exchange'],
                ]
            ]
        ]);

        $this->assertFalse($this->changeDetector->hasChanges());
    }

    /**
     * Helper method to setup mock database queues
     *
     * @param array $queues
     */
    private function setupDatabaseQueues(array $queues): void
    {
        $select = $this->createMock(Select::class);
        $select->method('distinct')->willReturnSelf();
        $select->method('from')->willReturnSelf();

        $this->connection->method('getTableName')->willReturn('queue');
        $this->connection->method('select')->willReturn($select);
        $this->connection->method('fetchCol')->willReturn($queues);
    }

    /**
     * Helper method to setup mock config queues
     *
     * @param array $queues
     */
    private function setupConfigQueues(array $queues): void
    {
        $config = [];
        foreach ($queues as $queue) {
            $config['exchange_' . $queue] = [
                'bindings' => [
                    ['destination' => $queue, 'destinationType' => 'queue']
                ]
            ];
        }
        $this->topologyConfigReader->method('read')->willReturn($config);
    }

    /**
     * Helper method to setup mock config queues with raw config
     *
     * @param array $config
     */
    private function setupConfigQueuesRaw(array $config): void
    {
        $this->topologyConfigReader->method('read')->willReturn($config);
    }
}
