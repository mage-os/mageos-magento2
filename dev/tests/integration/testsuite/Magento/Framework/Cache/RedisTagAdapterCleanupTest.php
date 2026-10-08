<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Cache;

use Magento\Framework\Cache\Frontend\Adapter\SymfonyAdapters\RedisTagAdapter;
use PHPUnit\Framework\TestCase;
use Predis\Client;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\Adapter\RedisAdapter;

/**
 * Exercises cache-key deletion and index cleanup against the same Redis server.
 */
class RedisTagAdapterCleanupTest extends TestCase
{
    /** @var Client */
    private Client $redis;

    /** @var RedisAdapter */
    private RedisAdapter $dataPool;

    /** @var string */
    private string $namespace;

    /** @var string */
    private string $id;

    /** @var string */
    private string $dataKey;

    /** @var string */
    private string $tagKey;

    /** @var string */
    private string $reverseKey;

    protected function setUp(): void
    {
        $port = (int)(getenv('MAGEOS_TEST_REDIS_PORT') ?: 6379);
        $this->redis = new Client(['host' => '127.0.0.1', 'port' => $port]);
        try {
            $this->redis->ping();
        } catch (\Exception $e) {
            $this->markTestSkipped('Redis is unavailable: ' . $e->getMessage());
        }

        $suffix = bin2hex(random_bytes(6));
        $this->namespace = 'rta_' . $suffix . '_';
        $this->id = 'rta_' . $suffix;
        $this->dataKey = $this->namespace . ':' . $this->id;
        $this->tagKey = 'cache:tags:' . $this->namespace . 'TAG';
        $this->reverseKey = 'cache:id_tags:' . $this->namespace . $this->id;

        $this->dataPool = new RedisAdapter($this->redis, $this->namespace);
        $item = $this->dataPool->getItem($this->id);
        $item->set('old');
        $this->dataPool->save($item);
        $this->redis->sadd($this->tagKey, $this->id);
        $this->redis->sadd($this->reverseKey, 'TAG');
        $this->redis->sadd('cache:all_ids', $this->id);
    }

    protected function tearDown(): void
    {
        if (isset($this->id)) {
            $this->redis->srem('cache:all_ids', $this->id);
            $this->redis->del([$this->dataKey, $this->tagKey, $this->reverseKey]);
        }
    }

    public function testConcurrentResaveKeepsItsTagMembership(): void
    {
        $adapter = $this->createAdapter(function (array $ids): bool {
            $this->assertSame([$this->id], $ids);
            $this->dataPool->deleteItems($ids);
            $item = $this->dataPool->getItem($this->id);
            $item->set('new');
            $this->dataPool->save($item);
            $this->redis->sadd($this->tagKey, $this->id);
            $this->redis->sadd($this->reverseKey, 'TAG');
            $this->redis->sadd('cache:all_ids', $this->id);
            return true;
        });

        $this->assertTrue($adapter->deleteByIds([$this->id], ['TAG']));
        $this->assertSame('new', $this->dataPool->getItem($this->id)->get());
        $this->assertSame(1, $this->redis->sismember($this->tagKey, $this->id));
        $this->assertSame(1, $this->redis->sismember($this->reverseKey, 'TAG'));
        $this->assertSame(1, $this->redis->sismember('cache:all_ids', $this->id));
    }

    public function testFailedDeletionKeepsLiveEntryIndexed(): void
    {
        $adapter = $this->createAdapter(function (): bool {
            return false;
        });

        $this->assertFalse($adapter->deleteByIds([$this->id], ['TAG']));
        $this->assertSame('old', $this->dataPool->getItem($this->id)->get());
        $this->assertSame(1, $this->redis->sismember($this->tagKey, $this->id));
        $this->assertSame(1, $this->redis->sismember($this->reverseKey, 'TAG'));
        $this->assertSame(1, $this->redis->sismember('cache:all_ids', $this->id));
    }

    public function testDeletedEntryWithoutReverseIndexIsSweptFromSourceTag(): void
    {
        $this->redis->del($this->reverseKey);
        $adapter = $this->createAdapter(function (array $ids): bool {
            return $this->dataPool->deleteItems($ids);
        });

        $this->assertTrue($adapter->deleteByIds([$this->id], ['TAG']));
        $this->assertSame(0, $this->redis->sismember($this->tagKey, $this->id));
        $this->assertSame(0, $this->redis->sismember('cache:all_ids', $this->id));
    }

    private function createAdapter(callable $deleteItems): RedisTagAdapter
    {
        $pool = $this->createMock(CacheItemPoolInterface::class);
        $pool->method('deleteItems')->willReturnCallback($deleteItems);
        $adapter = (new \ReflectionClass(RedisTagAdapter::class))->newInstanceWithoutConstructor();
        foreach (['redis' => $this->redis, 'namespace' => $this->namespace, 'cachePool' => $pool] as $key => $value) {
            (new \ReflectionProperty(RedisTagAdapter::class, $key))->setValue($adapter, $value);
        }

        return $adapter;
    }
}
