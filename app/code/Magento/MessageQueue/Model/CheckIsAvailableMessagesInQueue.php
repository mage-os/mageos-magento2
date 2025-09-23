<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\MessageQueue\Model;

use Magento\Framework\MessageQueue\CountableQueueInterface;
use Magento\Framework\MessageQueue\QueueRepository;

/**
 * Class CheckIsAvailableMessagesInQueue for checking messages available in queue
 */
class CheckIsAvailableMessagesInQueue
{
    /**
     * @var QueueRepository
     */
    private $queueRepository;

    /**
     * Initialize dependencies.
     *
     * @param QueueRepository $queueRepository
     */
    public function __construct(QueueRepository $queueRepository)
    {
        $this->queueRepository = $queueRepository;
    }

    /**
     * Checks if there is available messages in the queue
     *
     * @param string $connectionName connection name
     * @param string $queueName queue name
     * @return bool
     * @throws \LogicException if queue is not available
     */
    public function execute($connectionName, $queueName)
    {
        $queue = $this->queueRepository->get($connectionName, $queueName);
        if ($queue instanceof CountableQueueInterface) {
            return $queue->count() > 0;
        }
        if ($connectionName === 'stomp') {
            $queue->subscribeQueue();
            $message = $queue->readMessage();
            if ($message) {
                return true;
            }
        } else {
            $message = $queue->dequeue();
            if ($message) {
                $queue->reject($message);
                return true;
            }
        }
        return false;
    }
}
