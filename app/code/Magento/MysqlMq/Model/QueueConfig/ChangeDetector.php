<?php
/**
 * Copyright © Mage-OS, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MysqlMq\Model\QueueConfig;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\MessageQueue\Topology\Config\CompositeReader;

/**
 * Detects changes between queue configuration and database state.
 */
class ChangeDetector
{
    public function __construct(
        private readonly CompositeReader    $topologyConfigReader,
        private readonly ResourceConnection $resourceConnection
    ) {

    }

    /**
     * @return bool
     */
    public function hasChanges(): bool
    {
        $databaseQueues = $this->getQueuesFromDatabase();
        $configQueues = $this->getQueuesFromConfig();

        return $this->hasMissingQueues($databaseQueues, $configQueues);
    }

    /**
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
     * @return array
     */
    private function getQueuesFromConfig(): array
    {
        $queues = [];

        $config = $this->topologyConfigReader->read();
        foreach ($config as $exchangeName => $exchangeData) {
            if (isset($exchangeData['bindings']) && is_array($exchangeData['bindings'])) {
                foreach ($exchangeData['bindings'] as $binding) {
                    if (isset($binding['destination'], $binding['destinationType']) && $binding['destinationType'] === 'queue') {
                        $queues[] = $binding['destination'];
                    }
                }
            }
        }

        return array_unique($queues);
    }

    /***
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
