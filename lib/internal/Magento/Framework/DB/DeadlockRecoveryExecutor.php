<?php
/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\DB;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Adapter\DeadlockException;
use Magento\Framework\DB\Adapter\LockWaitException;

/**
 * Executes database operations with automatic retry handling for deadlock failures in concurrent environments.
 */
class DeadlockRecoveryExecutor implements DeadlockRecoveryExecutorInterface
{
    /**
     * @var int
     */
    private $attempts;

    /**
     * @var int
     */
    private $maxJitter;

    /**
     * @param int $attempts
     * @param int $maxJitter
     */
    public function __construct(
        int $attempts,
        int $maxJitter
    ) {
        $this->attempts = $attempts;
        $this->maxJitter = $maxJitter;
    }

    /**
     * @inheritdoc
     */
    public function execute(AdapterInterface $connection, callable $callable, array $args)
    {
        // @phpstan-ignore-next-line - false positive for missing return, loop exits with return or exception
        for ($attempt = 1; $attempt <= $this->attempts; $attempt++) {
            try {
                $connection->beginTransaction();

                $result = $callable(...$args);

                $connection->commit();

                return $result;
            } catch (DeadlockException|LockWaitException $e) {
                $connection->rollBack();
                if ($attempt >= $this->attempts) {
                    throw $e;
                }
                if ($this->maxJitter > 0) {
                    usleep(random_int(0, $this->maxJitter));
                }
            } catch (\Throwable $e) {
                $connection->rollBack();
                throw $e;
            }
        }
    }
}
