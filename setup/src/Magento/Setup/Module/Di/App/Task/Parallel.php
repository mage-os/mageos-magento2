<?php
/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Setup\Module\Di\App\Task;

/**
 * Runs independent units of compilation work in forked worker processes.
 *
 * Compilation forks only after the codebase has been loaded, so a worker inherits every loaded
 * class copy-on-write instead of paying to load it again. Workers communicate nothing back: each
 * unit of work writes its own output files, so there is no result to merge.
 */
class Parallel
{
    /**
     * Environment variable that forces compilation back into a single process.
     */
    public const DISABLE_ENV = 'MAGE_DI_COMPILE_SINGLE_PROCESS';

    /**
     * Whether worker processes can be used.
     *
     * @return bool
     */
    public static function isAvailable(): bool
    {
        return \function_exists('pcntl_fork')
            && \function_exists('pcntl_waitpid')
            && !\filter_var((string)\getenv(self::DISABLE_ENV), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * How many workers to use for the given amount of work, bounded by the available cores.
     *
     * @param int $items
     * @param int $minPerWorker
     * @param int $max
     * @return int
     */
    public static function workerCount(int $items, int $minPerWorker = 1, int $max = 16): int
    {
        if (!self::isAvailable() || $items < 2 || $minPerWorker < 1) {
            return 1;
        }

        return (int)\max(1, \min(self::cpuCount(), $max, \intdiv($items, $minPerWorker)));
    }

    /**
     * Run $work over every item, each in its own process, and wait for all of them.
     *
     * $prepare runs in the parent before each fork, so a caller whose items depend on state built
     * up by the preceding items can advance that state in the original order; the child then
     * inherits exactly the state its item would have seen sequentially.
     *
     * Falls back to running in-process whenever forking is unavailable or refused, so the result
     * is the same either way.
     *
     * @param array $items
     * @param callable $work
     * @param callable|null $prepare
     * @return void
     * @throws \RuntimeException
     */
    public static function each(array $items, callable $work, ?callable $prepare = null): void
    {
        $children = [];
        $failures = [];
        try {
            foreach ($items as $key => $item) {
                if ($prepare !== null) {
                    $prepare($item, $key);
                }
                if (!self::isAvailable() || \count($items) < 2) {
                    $work($item, $key);
                    continue;
                }
                $pid = \pcntl_fork();
                if ($pid === -1) {
                    // Fork refused (process or memory limits) - do this item here instead.
                    $work($item, $key);
                    continue;
                }
                if ($pid === 0) {
                    $status = 0;
                    try {
                        $work($item, $key);
                    } catch (\Throwable $e) {
                        \fwrite(STDERR, \sprintf('%s: %s%s', (string)$key, $e->getMessage(), PHP_EOL));
                        $status = 1;
                    }
                    // A forked worker must terminate here; returning would resume the parent's loop.
                    // phpcs:ignore Magento2.Security.LanguageConstruct.ExitUsage
                    exit($status);
                }
                $children[$pid] = $key;
            }
        } finally {
            // Reap every child even if the loop above threw, so no worker outlives the command.
            $failures = self::wait($children);
        }

        if ($failures) {
            throw new \RuntimeException(
                'Parallel compilation failed for: ' . \implode(', ', \array_map('strval', $failures))
            );
        }
    }

    /**
     * Wait for all children, returning the keys of the ones that did not succeed.
     *
     * @param array $children
     * @return array
     */
    private static function wait(array $children): array
    {
        $failures = [];
        foreach ($children as $pid => $key) {
            $status = 0;
            do {
                $result = \pcntl_waitpid($pid, $status);
                // Retry when the wait itself was interrupted by a signal.
            } while ($result === -1 && \pcntl_get_last_error() === PCNTL_EINTR);

            if ($result !== $pid || !\pcntl_wifexited($status) || \pcntl_wexitstatus($status) !== 0) {
                $failures[] = $key;
            }
        }

        return $failures;
    }

    /**
     * Number of usable CPUs.
     *
     * @return int
     */
    private static function cpuCount(): int
    {
        if (\is_readable('/proc/cpuinfo')) {
            $count = \substr_count((string)\file_get_contents('/proc/cpuinfo'), 'processor' . "\t");
            if ($count > 0) {
                return $count;
            }
        }

        return 4;
    }
}
