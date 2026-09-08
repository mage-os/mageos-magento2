<?php
/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Setup\Test\Unit\Module\Di\App\Task;

use Magento\Setup\Module\Di\App\Task\Parallel;
use PHPUnit\Framework\TestCase;

class ParallelTest extends TestCase
{
    protected function tearDown(): void
    {
        putenv(Parallel::DISABLE_ENV);
    }

    public function testWorkerProcessesCanBeDisabledByEnvironment()
    {
        putenv(Parallel::DISABLE_ENV . '=1');
        $this->assertFalse(Parallel::isAvailable());
        $this->assertSame(1, Parallel::workerCount(1000));
    }

    public function testWorkerCountIsOneForTrivialWorkloads()
    {
        $this->assertSame(1, Parallel::workerCount(1));
        $this->assertSame(1, Parallel::workerCount(0));
    }

    public function testWorkerCountNeverExceedsTheRequestedMaximum()
    {
        $this->assertLessThanOrEqual(2, Parallel::workerCount(1000, 1, 2));
    }

    public function testWorkerCountRespectsTheMinimumBatchSize()
    {
        // 10 items with a minimum of 5 per worker can justify at most 2 workers.
        $this->assertLessThanOrEqual(2, Parallel::workerCount(10, 5));
    }

    public function testEachProcessesEveryItemInProcessWhenDisabled()
    {
        putenv(Parallel::DISABLE_ENV . '=1');
        $seen = [];
        Parallel::each(['a' => 1, 'b' => 2, 'c' => 3], static function ($item, $key) use (&$seen) {
            $seen[$key] = $item;
        });
        $this->assertSame(['a' => 1, 'b' => 2, 'c' => 3], $seen);
    }

    public function testEachRunsPrepareBeforeEachItemInOrder()
    {
        putenv(Parallel::DISABLE_ENV . '=1');
        $order = [];
        Parallel::each(
            ['first', 'second'],
            static function ($item) use (&$order) {
                $order[] = 'work:' . $item;
            },
            static function ($item) use (&$order) {
                $order[] = 'prepare:' . $item;
            }
        );
        $this->assertSame(
            ['prepare:first', 'work:first', 'prepare:second', 'work:second'],
            $order
        );
    }

    public function testEachPropagatesFailuresFromInProcessWork()
    {
        putenv(Parallel::DISABLE_ENV . '=1');
        $this->expectException(\RuntimeException::class);
        Parallel::each([1, 2], static function () {
            throw new \RuntimeException('boom');
        });
    }

    public function testEachRunsWorkInSeparateProcessesWhenAvailable()
    {
        if (!Parallel::isAvailable()) {
            $this->markTestSkipped('pcntl is not available');
        }
        $dir = sys_get_temp_dir() . '/magento-parallel-test-' . getmypid();
        mkdir($dir);
        try {
            $parent = getmypid();
            Parallel::each([1, 2, 3], static function ($item) use ($dir) {
                file_put_contents($dir . '/' . $item, (string)getmypid());
            });
            $pids = [];
            foreach ([1, 2, 3] as $item) {
                $this->assertFileExists($dir . '/' . $item);
                $pids[] = file_get_contents($dir . '/' . $item);
            }
            $this->assertNotContains((string)$parent, $pids, 'work should not run in the parent');
            $this->assertCount(3, array_unique($pids), 'each item should get its own worker');
        } finally {
            array_map('unlink', glob($dir . '/*'));
            rmdir($dir);
        }
    }

    public function testEachReportsAFailingWorker()
    {
        if (!Parallel::isAvailable()) {
            $this->markTestSkipped('pcntl is not available');
        }
        $this->expectException(\RuntimeException::class);
        Parallel::each(['ok', 'bad'], static function ($item) {
            if ($item === 'bad') {
                throw new \RuntimeException('worker failed');
            }
        });
    }
}
