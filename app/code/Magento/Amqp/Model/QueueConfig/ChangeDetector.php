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
use Magento\Framework\MessageQueue\Topology\Config\CompositeReader;
use Magento\MessageQueue\Model\QueueConfig\ChangeDetectorInterface;
use PhpAmqpLib\Exception\AMQPProtocolChannelException;

/**
 * Detects changes between AMQP queue configuration and actual broker state.
 */
class ChangeDetector implements ChangeDetectorInterface
{
    /**
     * Constructor
     *
     * @param AmqpConfig $amqpConfig
     * @param CompositeReader $topologyConfigReader
     * @param ConnectionTypeResolver $connectionTypeResolver
     */
    public function __construct(
        private readonly AmqpConfig             $amqpConfig,
        private readonly CompositeReader        $topologyConfigReader,
        private readonly ConnectionTypeResolver $connectionTypeResolver
    ) {
    }

    /**
     * Check if there are changes between queue configuration and actual broker state
     *
     * @return bool
     */
    public function hasChanges(): bool
    {
        try {
            return !empty($this->getMissingQueues());
        } catch (Exception $e) {
            return false;
        }
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
        $queues = [];

        $config = $this->topologyConfigReader->read();
        foreach ($config as $exchangeName => $exchangeData) {
            if (isset($exchangeData['bindings']) && is_array($exchangeData['bindings'])) {
                foreach ($exchangeData['bindings'] as $binding) {
                    if (isset($binding['destination'], $binding['destinationType'])
                        && $binding['destinationType'] === 'queue'
                    ) {
                        // Determine connection: binding-level overrides exchange-level
                        $connection = $binding['connection'] ?? $exchangeData['connection'] ?? null;

                        if ($connection !== null) {
                            $connectionType = $this->connectionTypeResolver->getConnectionType($connection);

                            if ($connectionType === 'amqp') {
                                $queues[] = $binding['destination'];
                            }
                        }
                    }
                }
            }
        }

        return array_unique($queues);
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
        $channel = null;
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
                if ($channel !== null) {
                    try {
                        $channel->close();
                    } catch (Exception $closeException) {
                    }
                }
                return false;
            }
            throw $e;
        }
    }
}
