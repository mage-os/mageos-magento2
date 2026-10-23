<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Model\Dashboard;

use Magento\Backend\Model\Dashboard\StatisticsCache;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Serialize\Serializer\Json;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class StatisticsCacheTest extends TestCase
{
    /**
     * @var CacheInterface|MockObject
     */
    private $cache;

    /**
     * @var StatisticsCache
     */
    private $model;

    protected function setUp(): void
    {
        $this->cache = $this->createMock(CacheInterface::class);
        $this->model = new StatisticsCache($this->cache, new Json());
    }

    public function testLifetimeZeroBypassesCache(): void
    {
        $this->cache->expects($this->never())->method('load');
        $this->cache->expects($this->never())->method('save');

        $calls = 0;
        $result = $this->model->get('sales', ['store' => '1'], 0, function () use (&$calls) {
            $calls++;
            return ['lifetime' => 1.5];
        });

        $this->assertSame(['lifetime' => 1.5], $result);
        $this->assertSame(1, $calls);
    }

    public function testMissRunsLoaderAndSaves(): void
    {
        $this->cache->expects($this->once())->method('load')->willReturn(false);
        $this->cache->expects($this->once())
            ->method('save')
            ->with(
                '{"lifetime":2.5,"average":1.25}',
                $this->stringStartsWith('backend_dashboard_sales_'),
                [StatisticsCache::CACHE_TAG],
                3600
            );

        $result = $this->model->get('sales', [], 3600, fn () => ['lifetime' => 2.5, 'average' => 1.25]);

        $this->assertSame(['lifetime' => 2.5, 'average' => 1.25], $result);
    }

    public function testHitSkipsLoader(): void
    {
        $this->cache->expects($this->once())->method('load')->willReturn('{"revenue":10.0}');
        $this->cache->expects($this->never())->method('save');

        $result = $this->model->get('totals', ['period' => '24h'], 300, function () {
            $this->fail('Loader must not run on a cache hit');
        });

        $this->assertSame(['revenue' => 10.0], $result);
    }

    public function testLoadReturnsNullForAbsentOrMalformedEntries(): void
    {
        $this->cache->method('load')->willReturnOnConsecutiveCalls(false, '"scalar"');

        $this->assertNull($this->model->load('sales', []));
        $this->assertNull($this->model->load('sales', []));
    }

    public function testScopeParamsProduceDistinctIdsRegardlessOfOrder(): void
    {
        $ids = [];
        $this->cache->method('load')->willReturnCallback(function (string $id) use (&$ids) {
            $ids[] = $id;
            return false;
        });

        $this->model->load('sales', ['store' => '1', 'website' => '']);
        $this->model->load('sales', ['website' => '', 'store' => '1']);
        $this->model->load('sales', ['store' => '2', 'website' => '']);
        $this->model->load('totals', ['store' => '1', 'website' => '']);

        $this->assertSame($ids[0], $ids[1], 'Key order must not change the identifier');
        $this->assertNotSame($ids[0], $ids[2], 'Different scope must yield a different identifier');
        $this->assertNotSame($ids[0], $ids[3], 'Different key must yield a different identifier');
    }

    public function testCleanUsesTag(): void
    {
        $this->cache->expects($this->once())->method('clean')->with([StatisticsCache::CACHE_TAG]);
        $this->model->clean();
    }
}
