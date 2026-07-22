<?php
/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Cache\Test\Unit\Frontend\Adapter\SymfonyAdapters;

use Magento\Framework\Cache\Frontend\Adapter\SymfonyAdapters\RedisTagAdapter;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Predis\Client as PredisClient;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Unit test for RedisTagAdapter index maintenance / leak fixes.
 *
 * The adapter extracts a real Redis client from the Symfony pool in its constructor,
 * so we instantiate it without the constructor and inject a Predis-shaped test double.
 * This exercises the Predis code paths without requiring the redis extension.
 */
class RedisTagAdapterTest extends TestCase
{
    private const NS = '4e0_';

    /**
     * @var CacheItemPoolInterface|Stub
     */
    private $cachePoolMock;

    /**
     * Predis test double recording every command as [method, args]. Public props:
     * ->commands (log), ->sets (SMEMBERS source), ->sscanResponses (queued SSCAN replies).
     *
     * @var PredisClient
     */
    private PredisClient $redis;

    /**
     * @var RedisTagAdapter
     */
    private RedisTagAdapter $adapter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cachePoolMock = $this->createStub(CacheItemPoolInterface::class);
        $this->redis = $this->createRedisDouble();

        $this->adapter = (new \ReflectionClass(RedisTagAdapter::class))->newInstanceWithoutConstructor();
        $this->setPrivate('cachePool', $this->cachePoolMock);
        $this->setPrivate('namespace', self::NS);
        $this->setPrivate('redis', $this->redis);
        $this->setPrivate('useLua', false);
        $this->setPrivate('useLuaOnGc', false);
        $this->setPrivate('luaHelper', null);
    }

    /**
     * deleteByIds() must remove every id from its tag sets, from all_ids, and delete the
     * reverse index — not just SREM all_ids (which was the leak).
     */
    public function testDeleteByIdsCleansTagSetsAndReverseIndex(): void
    {
        $this->redis->sets['cache:id_tags:4e0_ID1'] = ['BLOCK_HTML', 'CAT_P'];
        $this->redis->sets['cache:id_tags:4e0_ID2'] = ['MAGE'];

        $this->cachePoolMock->method('deleteItems')->willReturn(true);

        $this->adapter->deleteByIds(['ID1', 'ID2']);

        // Reverse index was read to discover memberships
        $this->assertCommand('smembers', ['cache:id_tags:4e0_ID1']);
        $this->assertCommand('smembers', ['cache:id_tags:4e0_ID2']);

        // ID1 removed from all_ids + both of its tag sets, reverse index deleted
        $this->assertCommand('srem', ['cache:all_ids', 'ID1']);
        $this->assertCommand('srem', ['cache:tags:4e0_BLOCK_HTML', 'ID1']);
        $this->assertCommand('srem', ['cache:tags:4e0_CAT_P', 'ID1']);
        $this->assertCommand('del', ['cache:id_tags:4e0_ID1']);

        // ID2 removed from all_ids + its tag set, reverse index deleted
        $this->assertCommand('srem', ['cache:all_ids', 'ID2']);
        $this->assertCommand('srem', ['cache:tags:4e0_MAGE', 'ID2']);
        $this->assertCommand('del', ['cache:id_tags:4e0_ID2']);
    }

    public function testDeleteByIdsEmptyIsNoop(): void
    {
        $this->assertTrue($this->adapter->deleteByIds([]));
        $this->assertSame([], $this->redis->commands);
    }

    /**
     * onSave() must EXPIRE the reverse index when a lifetime is given, so it can't outlive
     * its data key and leak on passive expiry.
     */
    public function testOnSaveSetsReverseIndexTtlWhenLifetimeGiven(): void
    {
        $this->adapter->onSave('ID1', ['BLOCK_HTML'], 60);

        $this->assertCommand('sadd', ['cache:all_ids', 'ID1']);
        $this->assertCommand('sadd', ['cache:tags:4e0_BLOCK_HTML', 'ID1']);
        $this->assertCommand('del', ['cache:id_tags:4e0_ID1']);
        $this->assertCommand('sadd', ['cache:id_tags:4e0_ID1', 'BLOCK_HTML']);
        // lifetime 60 + ID_TAGS_TTL_BUFFER (3600)
        $this->assertCommand('expire', ['cache:id_tags:4e0_ID1', 3660]);
    }

    /**
     * Without a lifetime the reverse index stays persistent (matching a persistent data key):
     * no EXPIRE is issued. This preserves the pre-fix behavior for un-lifetimed entries.
     */
    public function testOnSaveDoesNotSetTtlWhenLifetimeNull(): void
    {
        $this->adapter->onSave('ID1', ['BLOCK_HTML']);

        $this->assertNoCommand('expire');
    }

    public function testOnSaveDoesNotSetTtlWhenLifetimeZero(): void
    {
        $this->adapter->onSave('ID1', ['BLOCK_HTML'], 0);

        $this->assertNoCommand('expire');
    }

    /**
     * garbageCollect() scans all_ids and reaps the bookkeeping of ids whose data key is gone,
     * while leaving live ids untouched.
     */
    public function testGarbageCollectReapsOrphanedIds(): void
    {
        $this->redis->sscanResponses = [[0, ['LIVE', 'DEAD']]];
        $this->redis->sets['cache:id_tags:4e0_DEAD'] = ['MAGE'];

        $this->cachePoolMock->method('hasItem')->willReturnMap([
            ['LIVE', true],
            ['DEAD', false],
        ]);

        $cleaned = $this->adapter->garbageCollect();

        $this->assertSame(1, $cleaned);
        // DEAD reaped
        $this->assertCommand('srem', ['cache:all_ids', 'DEAD']);
        $this->assertCommand('srem', ['cache:tags:4e0_MAGE', 'DEAD']);
        $this->assertCommand('del', ['cache:id_tags:4e0_DEAD']);
        // LIVE left alone
        $this->assertNoCommand('del', ['cache:id_tags:4e0_LIVE']);
        $this->assertNoCommand('srem', ['cache:all_ids', 'LIVE']);
    }

    public function testGarbageCollectWithNoOrphansCleansNothing(): void
    {
        $this->redis->sscanResponses = [[0, ['LIVE']]];
        $this->cachePoolMock->method('hasItem')->willReturn(true);

        $this->assertSame(0, $this->adapter->garbageCollect());
        $this->assertNoCommand('del');
    }

    /**
     * Build a Predis-shaped test double (and matching pipeline) that records every command.
     *
     * @return PredisClient
     */
    private function createRedisDouble(): PredisClient
    {
        return new class extends PredisClient {
            /** @var array<int, array{0:string,1:array}> */
            public array $commands = [];

            /** @var array<string, array> key => members (SMEMBERS result) */
            public array $sets = [];

            /** @var array<int, array{0:int,1:array}> queued [cursor, members] SSCAN responses */
            public array $sscanResponses = [];

            // phpcs:ignore Magento2.Functions.DiscouragedFunction
            public function __construct()
            {
                // Bypass Predis\Client construction; this double intercepts every call.
            }

            public function pipeline(...$args)
            {
                $parent = $this;

                return new class ($parent) {
                    /** @var object */
                    private $parent;

                    /** @var array<int, array{0:string,1:array}> */
                    private array $queued = [];

                    public function __construct($parent)
                    {
                        $this->parent = $parent;
                    }

                    public function execute(): array
                    {
                        $results = [];
                        foreach ($this->queued as [$method, $args]) {
                            $results[] = $method === 'smembers' ? ($this->parent->sets[$args[0]] ?? []) : true;
                        }

                        return $results;
                    }

                    public function __call($method, $arguments)
                    {
                        $method = strtolower($method);
                        $this->queued[] = [$method, $arguments];
                        $this->parent->commands[] = [$method, $arguments];

                        return $this;
                    }
                };
            }

            public function __call($method, $arguments)
            {
                $method = strtolower($method);
                $this->commands[] = [$method, $arguments];

                return match ($method) {
                    'smembers' => $this->sets[$arguments[0]] ?? [],
                    'sscan' => array_shift($this->sscanResponses) ?: [0, []],
                    default => true,
                };
            }
        };
    }

    private function setPrivate(string $property, $value): void
    {
        $ref = new \ReflectionProperty(RedisTagAdapter::class, $property);
        $ref->setValue($this->adapter, $value);
    }

    private function assertCommand(string $method, array $args): void
    {
        $this->assertContains(
            [$method, $args],
            $this->redis->commands,
            sprintf('Expected command %s(%s) was not issued', $method, implode(', ', $args))
        );
    }

    private function assertNoCommand(string $method, ?array $args = null): void
    {
        foreach ($this->redis->commands as $command) {
            if ($command[0] !== $method) {
                continue;
            }
            if ($args === null || $command[1] === $args) {
                $this->fail(sprintf('Did not expect command %s(%s)', $method, implode(', ', $args ?? $command[1])));
            }
        }
        $this->addToAssertionCount(1);
    }
}
