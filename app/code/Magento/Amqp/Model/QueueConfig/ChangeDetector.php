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
use Psr\Log\LoggerInterface;

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
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly AmqpConfig             $amqpConfig,
        private readonly CompositeReader        $topologyConfigReader,
        private readonly ConnectionTypeResolver $connectionTypeResolver,
        private readonly LoggerInterface        $logger
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
        } catch (\LogicException $e) {
            $this->logger->info(
                'AMQP queue status check skipped: ' . $e->getMessage()
            );
            return false;
        } catch (Exception $e) {
            $this->logger->warning(
                'Failed to check AMQP queue status: ' . $e->getMessage()
            );
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
            if (!isset($exchangeData['bindings']) || !is_array($exchangeData['bindings'])) {
                continue;
            }

            foreach ($exchangeData['bindings'] as $binding) {
                $queueName = $this->extractAmqpQueueFromBinding($binding, $exchangeData);
                if ($queueName !== null) {
                    $queues[] = $queueName;
                }
            }
        }

        return array_unique($queues);
    }

    /**
     * Extract AMQP queue name from binding if it's an AMQP queue
     *
     * @param array $binding
     * @param array $exchangeData
     * @return string|null
     */
    private function extractAmqpQueueFromBinding(array $binding, array $exchangeData): ?string
    {
        if (!isset($binding['destination'], $binding['destinationType'])) {
            return null;
        }

        if ($binding['destinationType'] !== 'queue') {
            return null;
        }

        // Determine connection: binding-level overrides exchange-level
        $connection = $binding['connection'] ?? $exchangeData['connection'] ?? null;

        if ($connection === null) {
            return null;
        }

        $connectionType = $this->connectionTypeResolver->getConnectionType($connection);

        return $connectionType === 'amqp' ? $binding['destination'] : null;
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
                        // Channel is already broken after 404 - close may fail, which is expected
                        $this->logger->debug(
                            'Failed to close AMQP channel, 404 response, this is expected: ' . $closeException->getMessage()
                        );
                    }
                }
                return false;
            }
            throw $e;
        }
    }
}
