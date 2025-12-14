<?php
/**
 * Copyright © Mage-OS, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Amqp\Model\QueueConfig;

use Exception;
use Magento\Framework\Amqp\Config as AmqpConfig;
use Magento\Framework\MessageQueue\ConnectionTypeResolver;
use Magento\Framework\MessageQueue\Topology\ConfigInterface as TopologyConfigInterface;
use PhpAmqpLib\Exception\AMQPProtocolChannelException;

/**
 * Detects changes between AMQP queue configuration and actual broker state.
 */
class ChangeDetector
{
    /**
     * @param AmqpConfig $amqpConfig
     * @param TopologyConfigInterface $topologyConfig
     * @param ConnectionTypeResolver $connectionTypeResolver
     */
    public function __construct(
        private readonly AmqpConfig              $amqpConfig,
        private readonly TopologyConfigInterface $topologyConfig,
        private readonly ConnectionTypeResolver  $connectionTypeResolver
    ) {
    }

    /**
     * Check if there are missing queues in AMQP broker
     *
     * @return bool
     * @throws Exception
     */
    public function hasChanges(): bool
    {
        return !empty($this->getMissingQueues());
    }

    /**
     * Get list of queues that are missing in AMQP broker
     *
     * @return array
     * @throws Exception
     */
    public function getMissingQueues(): array
    {
        $configuredQueues = $this->getQueuesFromConfig();
        $missingQueues = [];

        if (empty($configuredQueues)) {
            return [];
        }

        foreach ($configuredQueues as $queueName) {
            if (!$this->verifyQueueExists($queueName)) {
                $missingQueues[] = $queueName;
            }
        }

        return $missingQueues;
    }

    /**
     * Get list of AMQP queues from topology configuration
     *
     * @return array
     */
    private function getQueuesFromConfig(): array
    {
        $allQueues = $this->topologyConfig->getQueues();
        $amqpQueues = [];

        foreach ($allQueues as $queue) {
            $connectionType = $this->connectionTypeResolver->getConnectionType(
                $queue->getConnection()
            );

            if ($connectionType === 'amqp') {
                $amqpQueues[] = $queue->getName();
            }
        }

        return array_unique($amqpQueues);
    }

    /**
     * Verify if a queue exists in AMQP broker using passive mode
     *
     * @param string $queueName
     * @return bool
     * @throws Exception
     */
    private function verifyQueueExists(string $queueName): bool
    {
        try {
            $channel = $this->amqpConfig->getChannel();

            // Passive mode: inspect queue without creating it
            $channel->queue_declare(
                $queueName,
                true,
                false,
                false,
                false,
                false
            );

            return true;

        } catch (AMQPProtocolChannelException $e) {
            if ($e->getCode() === 404) {
                return false;
            }
            throw $e;
        }
    }
}
