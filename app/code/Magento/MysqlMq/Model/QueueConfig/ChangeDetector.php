<?php
/**
 * Copyright © Mage-OS, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MysqlMq\Model\QueueConfig;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\MessageQueue\Topology\Config\CompositeReader;
use Magento\MessageQueue\Model\QueueConfig\ChangeDetectorInterface;

/**
 * Detects changes between queue configuration and database state.
 */
class ChangeDetector implements ChangeDetectorInterface
{
    /**
     * Constructor
     *
     * @param CompositeReader $topologyConfigReader
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        private readonly CompositeReader    $topologyConfigReader,
        private readonly ResourceConnection $resourceConnection
    ) {
    }

    /**
     * Check if there are changes between queue configuration and database state
     *
     * @return bool
     */
    public function hasChanges(): bool
    {
        $databaseQueues = $this->getQueuesFromDatabase();
        $configQueues = $this->getQueuesFromConfig();

        return $this->hasMissingQueues($databaseQueues, $configQueues);
    }

    /**
     * Retrieve queue names from the database
     *
     * @return array
     */
    private function getQueuesFromDatabase(): array
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $connection->getTableName('queue');
        $select = $connection->select()->distinct()->from($tableName, ['name']);

        return $connection->fetchCol($select);
    }

    /**
     * Retrieve queue names from the configuration
     *
     * @return array
     */
    private function getQueuesFromConfig(): array
    {
        $queues = [];

        $config = $this->topologyConfigReader->read();
        foreach ($config as $exchangeName => $exchangeData) {
            if (isset($exchangeData['bindings']) && is_array($exchangeData['bindings'])) {
                foreach ($exchangeData['bindings'] as $binding) {
                    if (isset($binding['destination'], $binding['destinationType'])
                        && $binding['destinationType'] === 'queue'
                    ) {
                        $queues[] = $binding['destination'];
                    }
                }
            }
        }

        return array_unique($queues);
    }

    /**
     * Check if there are queues in config that are missing from database
     *
     * @param array $databaseQueues
     * @param array $configQueues
     * @return bool
     */
    private function hasMissingQueues(array $databaseQueues, array $configQueues): bool
    {
        $missingQueues = array_diff($configQueues, $databaseQueues);
        return !empty($missingQueues);
    }
}
