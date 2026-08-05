<?php
/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Cache\Frontend\Adapter\SymfonyAdapters;

use Magento\Framework\Cache\Frontend\Adapter\OptimizedPredisClient;
use Predis\Client as PredisClient;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;

/**
 * Redis-specific tag adapter
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RedisTagAdapter implements TagAdapterInterface
{
    private const TAG_INDEX_PREFIX = 'cache:tags:';
    private const ALL_IDS_SET = 'cache:all_ids';
    private const REVERSE_INDEX_PREFIX = 'cache:id_tags:';

    /**
     * Extra seconds the reverse index (cache:id_tags:*) is allowed to outlive its data key.
     *
     * Without a TTL these sets leak forever once the data key expires passively, because
     * passive expiry fires no event the adapter can hook (onRemove() only runs on explicit
     * removes). The buffer keeps the reverse index discoverable by garbageCollect() for a
     * while after the data key is gone, so residual tag-set memberships can still be reaped.
     */
    private const ID_TAGS_TTL_BUFFER = 3600;

    /**
     * SUNION chunk size
     * On large data sets SUNION slows down considerably when used with too many arguments
     * @see vendor/colinmollenhour/cache-backend-redis/Cm/Cache/Backend/Redis.php line 92
     */
    private const SUNION_CHUNK_SIZE = 500;

    /**
     * Maximum number of IDs to be removed at a time - matches Zend's $_removeChunkSize
     * @see vendor/colinmollenhour/cache-backend-redis/Cm/Cache/Backend/Redis.php line 99
     */
    private const REMOVE_CHUNK_SIZE = 10000;

    /**
     * Lua's unpack() limit - matches Zend's $_luaMaxCStack
     * @see vendor/colinmollenhour/cache-backend-redis/Cm/Cache/Backend/Redis.php line 121
     */
    private const LUA_MAX_CSTACK = 5000;

    /**
     * Lua script for cleaning cache entries matching ANY tags (OR logic)
     *
     * This matches Zend's LUA_CLEAN_SH1 implementation exactly
     * (vendor/colinmollenhour/cache-backend-redis/Cm/Cache/Backend/Redis.php line 780-798)
     *
     * Performance: Single atomic Redis operation, ~10-15% faster than PHP implementation
     */
    private const LUA_CLEAN_MATCHING_ANY_TAGS = <<<'LUA'
-- KEYS: array of tags to match (e.g., ["product", "category", "config"])
-- ARGV[1]: tag prefix (e.g., "cache:tags:")
-- ARGV[2]: namespace prefix (e.g., "69d_")
-- ARGV[3]: data key prefix (e.g., "69d_:" - Symfony appends ':' to the namespace)
-- ARGV[4]: reverse index prefix (e.g., "cache:id_tags:")
-- ARGV[5]: all_ids set key (e.g., "cache:all_ids")

local tag_prefix = ARGV[1]
local namespace = ARGV[2]
local data_prefix = ARGV[3]
local reverse_prefix = ARGV[4]
local all_ids_key = ARGV[5]

-- Build prefixed tag keys
local prefixed_tags = {}
for i, tag in ipairs(KEYS) do
    prefixed_tags[i] = tag_prefix .. namespace .. tag
end

-- Get IDs matching ANY of the tags using SUNION
local ids_to_delete = redis.call('SUNION', unpack(prefixed_tags))

if #ids_to_delete == 0 then
    return 0
end

-- Delete cache items and all their tag bookkeeping
local deleted = 0
for _, id in ipairs(ids_to_delete) do
    redis.call('DEL', data_prefix .. id)
    local reverse_key = reverse_prefix .. namespace .. id
    local id_tags = redis.call('SMEMBERS', reverse_key)
    for _, tag in ipairs(id_tags) do
        redis.call('SREM', tag_prefix .. namespace .. tag, id)
    end
    redis.call('DEL', reverse_key)
    redis.call('SREM', all_ids_key, id)
    deleted = deleted + 1
end

-- The script is atomic, so the source tag sets can be dropped wholesale: every
-- member was just deleted and no concurrent save can interleave. This also reaps
-- stale members whose reverse index already expired.
redis.call('DEL', unpack(prefixed_tags))

return deleted
LUA;

    /**
     * Lua script for cleaning cache entries matching ANY tags within a scope (OR + AND logic)
     *
     * Logic: (tag1 OR tag2 OR ...) AND scopeTag
     *
     * Performance: Single atomic Redis operation with scope filtering
     */
    private const LUA_CLEAN_MATCHING_ANY_TAGS_WITH_SCOPE = <<<'LUA'
-- KEYS: array of tags to match (e.g., ["product", "category"])
-- ARGV[1]: tag prefix (e.g., "cache:tags:")
-- ARGV[2]: namespace prefix (e.g., "69d_")
-- ARGV[3]: scope tag (e.g., "FPC")
-- ARGV[4]: data key prefix (e.g., "69d_:" - Symfony appends ':' to the namespace)
-- ARGV[5]: reverse index prefix (e.g., "cache:id_tags:")
-- ARGV[6]: all_ids set key (e.g., "cache:all_ids")

local tag_prefix = ARGV[1]
local namespace = ARGV[2]
local scope_tag = ARGV[3]
local data_prefix = ARGV[4]
local reverse_prefix = ARGV[5]
local all_ids_key = ARGV[6]

-- Build prefixed tag keys
local prefixed_tags = {}
for i, tag in ipairs(KEYS) do
    prefixed_tags[i] = tag_prefix .. namespace .. tag
end

-- Step 1: Get IDs matching ANY of the tags using SUNION
local any_ids = redis.call('SUNION', unpack(prefixed_tags))

if #any_ids == 0 then
    return 0
end

-- Step 2: Get IDs matching the scope tag
local scope_key = tag_prefix .. namespace .. scope_tag
local scope_ids = redis.call('SMEMBERS', scope_key)

if #scope_ids == 0 then
    return 0
end

-- Step 3: Intersect in Lua (find IDs in both sets)
local scope_set = {}
for _, id in ipairs(scope_ids) do
    scope_set[id] = true
end

local filtered_ids = {}
for _, id in ipairs(any_ids) do
    if scope_set[id] then
        table.insert(filtered_ids, id)
    end
end

if #filtered_ids == 0 then
    return 0
end

-- Step 4: Delete filtered IDs and all their tag bookkeeping. The source tag sets
-- cannot be dropped wholesale here (non-scope members must survive), so each id is
-- also SREMed from the source sets explicitly to reap stale members whose reverse
-- index already expired.
local deleted = 0
for _, id in ipairs(filtered_ids) do
    redis.call('DEL', data_prefix .. id)
    local reverse_key = reverse_prefix .. namespace .. id
    local id_tags = redis.call('SMEMBERS', reverse_key)
    for _, tag in ipairs(id_tags) do
        redis.call('SREM', tag_prefix .. namespace .. tag, id)
    end
    redis.call('DEL', reverse_key)
    redis.call('SREM', all_ids_key, id)
    for _, tag_key in ipairs(prefixed_tags) do
        redis.call('SREM', tag_key, id)
    end
    redis.call('SREM', scope_key, id)
    deleted = deleted + 1
end

return deleted
LUA;

    /**
     * @var \Redis|\RedisCluster|PredisClient|OptimizedPredisClient
     */
    private \Redis|\RedisCluster|PredisClient|OptimizedPredisClient $redis;

    /**
     * @var string
     */
    private string $namespace;

    /**
     * @var CacheItemPoolInterface
     */
    private CacheItemPoolInterface $cachePool;

    /**
     * @var RedisLuaHelper|null
     */
    private ?RedisLuaHelper $luaHelper = null;

    /**
     * @var bool
     */
    private bool $useLua;

    /**
     * @var bool
     */
    private bool $useLuaOnGc;

    /**
     * @param CacheItemPoolInterface $cachePool
     * @param string $namespace Cache namespace/prefix
     * @param bool $useLua Enable Lua scripts for cache operations
     * @param bool $useLuaOnGc Enable Lua scripts for garbage collection
     */
    public function __construct(
        CacheItemPoolInterface $cachePool,
        string $namespace = '',
        bool $useLua = false,
        bool $useLuaOnGc = false
    ) {
        $this->cachePool = $cachePool;
        $this->namespace = $namespace;
        $this->redis = $this->extractRedisClient($cachePool);

        if ($this->isPredisClient()) {
            $this->useLua = false;
            $this->useLuaOnGc = false;
        } else {
            $this->useLua = $useLua;
            $this->useLuaOnGc = $useLuaOnGc;
        }

        if (($this->useLua || $this->useLuaOnGc) && !$this->isPredisClient()) {
            $this->luaHelper = new RedisLuaHelper($this->redis, true);
        }
    }

    /**
     * Extract Redis client from Symfony cache adapter
     *
     * @param CacheItemPoolInterface $cachePool
     * @return \Redis|\RedisCluster|PredisClient|OptimizedPredisClient
     * @throws \RuntimeException If Redis client cannot be extracted
     */
    private function extractRedisClient(
        CacheItemPoolInterface $cachePool
    ): \Redis|\RedisCluster|PredisClient|OptimizedPredisClient {
        $adapter = $cachePool;
        if ($adapter instanceof TagAwareAdapter) {
            $reflection = new \ReflectionClass($adapter);
            $poolProperty = $reflection->getProperty('pool');
            $adapter = $poolProperty->getValue($adapter);
        }

        // Get Redis client from RedisAdapter
        if ($adapter instanceof RedisAdapter) {
            $reflection = new \ReflectionClass($adapter);
            $redisProperty = $reflection->getProperty('redis');
            $redis = $redisProperty->getValue($adapter);

            if ($redis instanceof \Redis || $redis instanceof \RedisCluster ||
                $redis instanceof PredisClient || $redis instanceof OptimizedPredisClient) {
                return $redis;
            }
        }

        throw new \RuntimeException('Could not extract Redis client from cache adapter');
    }

    /**
     * Get prefixed tag name for Redis SET key
     *
     * @param string $tag
     * @return string
     */
    private function getTagKey(string $tag): string
    {
        return self::TAG_INDEX_PREFIX . $this->namespace . $tag;
    }

    /**
     * Get the reverse-index SET key (id -> tags) for a cache id
     *
     * @param string $id
     * @return string
     */
    private function reverseIndexKey(string $id): string
    {
        return self::REVERSE_INDEX_PREFIX . $this->namespace . $id;
    }

    /**
     * Check if using Predis client (vs phpredis extension)
     *
     * @return bool
     */
    private function isPredisClient(): bool
    {
        return $this->redis instanceof PredisClient || $this->redis instanceof OptimizedPredisClient;
    }

    /**
     * Create Redis pipeline compatible with both phpredis and Predis
     *
     * @return \Redis|object Predis pipeline object
     */
    private function createPipeline()
    {
        if ($this->isPredisClient()) {
            return $this->redis->pipeline();
        }

        return $this->redis->multi(\Redis::PIPELINE);
    }

    /**
     * Execute Redis pipeline compatible with both phpredis and Predis
     *
     * @param \Redis|object $pipeline
     * @return mixed
     */
    private function executePipeline($pipeline)
    {
        if ($pipeline instanceof PredisClient || method_exists($pipeline, 'execute')) {
            // Predis pipeline
            return $pipeline->execute();
        }

        // phpredis pipeline
        return $pipeline->exec();
    }

    /**
     * @inheritDoc
     *
     * Uses Redis SINTER for efficient set intersection (true AND logic)
     */
    public function getIdsMatchingTags(array $tags): array
    {
        if (empty($tags)) {
            return [];
        }

        // Build tag keys for Redis SINTER
        $tagKeys = array_map([$this, 'getTagKey'], $tags);

        // Redis SINTER returns IDs present in ALL sets
        $ids = $this->redis->sinter($tagKeys);

        return is_array($ids) ? $ids : [];
    }

    /**
     * @inheritDoc
     *
     * Uses Redis SUNION for efficient set union (OR logic)
     *
     * OPTIMIZED: Single tag uses SMEMBERS (faster), multiple tags use SUNION
     * Redis SUNION already returns unique values, no need for array_unique()
     */
    public function getIdsMatchingAnyTags(array $tags): array
    {
        if (empty($tags)) {
            return [];
        }

        // OPTIMIZATION: For single tag, use SMEMBERS directly (faster than SUNION)
        if (count($tags) === 1) {
            $ids = $this->redis->sMembers($this->getTagKey($tags[0]));
            return is_array($ids) ? $ids : [];
        }

        // Matches Zend's implementation to prevent Redis slowdowns
        // @see vendor/colinmollenhour/cache-backend-redis/Cm/Cache/Backend/Redis.php line 777-778
        if (count($tags) > self::SUNION_CHUNK_SIZE) {
            $allIds = [];
            $chunks = array_chunk($tags, self::SUNION_CHUNK_SIZE);

            foreach ($chunks as $chunk) {
                $tagKeys = array_map([$this, 'getTagKey'], $chunk);
                $chunkIds = $this->redis->sUnion($tagKeys);
                $chunkIds = is_array($chunkIds) ? $chunkIds : [];

                // phpcs:ignore Magento2.Performance.ForeachArrayMerge
                $allIds = array_merge($allIds, $chunkIds);
            }

            return array_unique($allIds);
        }

        $tagKeys = array_map([$this, 'getTagKey'], $tags);
        $ids = $this->redis->sUnion($tagKeys);

        return is_array($ids) ? $ids : [];
    }

    /**
     * @inheritDoc
     *
     * Gets all IDs and removes those matching any of the given tags
     */
    public function getIdsNotMatchingTags(array $tags): array
    {
        if (empty($tags)) {
            // Return all IDs if no tags specified
            $allIds = $this->redis->smembers(self::ALL_IDS_SET);
            return is_array($allIds) ? $allIds : [];
        }

        $tagKeys = array_map([$this, 'getTagKey'], $tags);

        // Prepend the all_ids set as first argument
        array_unshift($tagKeys, self::ALL_IDS_SET);

        // Call SDIFF: returns IDs in ALL_IDS_SET but NOT in any tag sets
        $result = call_user_func_array([$this->redis, 'sdiff'], $tagKeys);

        return is_array($result) ? $result : [];
    }

    /**
     * @inheritDoc
     *
     * OPTIMIZED: Uses Redis pipeline for large batches
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function deleteByIds(array $ids, array $sourceTags = []): bool
    {
        if (empty($ids)) {
            return true;
        }

        // Drop the ids from the tag sets they were discovered from. cleanupIndicesForIds()
        // can only SREM memberships still recorded in each id's reverse index, so stale
        // members (reverse index expired) would otherwise stay in these sets forever.
        $this->removeIdsFromTagSets($sourceTags, $ids);

        // Matches Zend's implementation to prevent Redis blocking and memory issues
        // @see vendor/colinmollenhour/cache-backend-redis/Cm/Cache/Backend/Redis.php line 809-825
        if (count($ids) > self::REMOVE_CHUNK_SIZE) {
            $chunks = array_chunk($ids, self::REMOVE_CHUNK_SIZE);
            $success = true;

            foreach ($chunks as $chunk) {
                // Delete cache items for this chunk
                if (!$this->cachePool->deleteItems($chunk)) {
                    $success = false;
                }

                // Remove the chunk's tag bookkeeping (tag-set members, all_ids, reverse index)
                $this->cleanupIndicesForIds($chunk);

                // Commit each chunk separately (important for large operations)
                if (method_exists($this->cachePool, 'commit')) {
                    $this->cachePool->commit();
                }
            }

            return $success;
        }

        $success = $this->cachePool->deleteItems($ids);

        // Remove all tag bookkeeping for the deleted ids. Deleting the data keys
        // alone leaves their reverse index and tag-set memberships behind, which
        // is the primary source of unbounded Redis growth on tag invalidation.
        $this->cleanupIndicesForIds($ids);

        // Ensure changes are committed immediately (important for MFTF and tests)
        if (method_exists($this->cachePool, 'commit')) {
            $this->cachePool->commit();
        }

        return $success;
    }

    /**
     * Remove all tag bookkeeping for the given ids
     *
     * For each id: remove its membership from every tag set it belonged to (discovered
     * via the reverse index), remove it from all_ids, and delete the reverse index key.
     * Individual Redis SET members cannot expire, so leaving them behind leaks memory
     * without bound; this is the batch equivalent of onRemove().
     *
     * Uses two pipelines (SMEMBERS, then SREM/DEL) so it works with both phpredis and
     * Predis. It is not atomic: a concurrent onSave() for the same id may re-create the
     * reverse index between the two passes, but that id self-heals on its next save.
     *
     * Processes ids in sub-chunks so a large invalidation (deleteByIds() passes up to
     * REMOVE_CHUNK_SIZE ids at once) cannot buffer an unbounded pipeline: with many
     * tags per entry the second pipeline is ids x (tags + 2) commands, so bounding ids
     * caps both the client-side command buffer and the single-burst load on Redis.
     *
     * @param array $ids
     * @return void
     */
    private function cleanupIndicesForIds(array $ids): void
    {
        if (empty($ids)) {
            return;
        }

        foreach (array_chunk(array_values($ids), 1000) as $chunk) {
            // First pass: read the tags associated with each id from its reverse index.
            $pipeline = $this->createPipeline();
            foreach ($chunk as $id) {
                $pipeline->smembers($this->reverseIndexKey($id));
            }
            $tagLists = $this->executePipeline($pipeline);
            if (!is_array($tagLists)) {
                $tagLists = [];
            }

            // Second pass: drop each id from all_ids and every tag set it belonged to,
            // then delete the reverse index itself.
            $pipeline = $this->createPipeline();
            foreach ($chunk as $i => $id) {
                $pipeline->srem(self::ALL_IDS_SET, $id);
                $tags = $tagLists[$i] ?? null;
                if (is_array($tags)) {
                    foreach ($tags as $tag) {
                        $pipeline->srem($this->getTagKey($tag), $id);
                    }
                }
                $pipeline->del($this->reverseIndexKey($id));
            }
            $this->executePipeline($pipeline);
        }
    }

    /**
     * Clean cache entries matching ANY of the given tags (OR logic)
     *
     * @param array $tags Tags to match (OR logic)
     * @return bool
     */
    public function cleanMatchingAnyTags(array $tags): bool
    {
        if (empty($tags)) {
            return true;
        }

        // Lua path (if enabled) - matches Zend's Lua script (line 776-801)
        if ($this->useLua && $this->luaHelper && $this->luaHelper->isEnabled()) {
            try {
                $deleted = $this->cleanMatchingAnyTagsLua($tags);

                // Ensure changes are committed
                if (method_exists($this->cachePool, 'commit')) {
                    $this->cachePool->commit();
                }

                return $deleted >= 0; // Lua returns number of items deleted
            // phpcs:disable Magento2.CodeAnalysis.EmptyBlock
            } catch (\Exception $e) {
                // Intentional: Fall through to PHP implementation on Lua failure
            }
            // phpcs:enable Magento2.CodeAnalysis.EmptyBlock
        }

        // PHP path (fallback) - matches Zend's PHP path (line 804-812)
        $ids = $this->getIdsMatchingAnyTags($tags);

        if (empty($ids)) {
            return true;
        }

        // Batch delete - exactly like Zend's _removeByIds (line 751-768)
        $success = $this->deleteByIds($ids, $tags);

        // Ensure changes are committed to underlying pool
        if (method_exists($this->cachePool, 'commit')) {
            $this->cachePool->commit();
        }

        return $success;
    }

    /**
     * Clean cache entries matching ANY tags within a scope (OR + AND logic)
     *
     * @param array $tags Tags to match (OR logic)
     * @param string $scopeTag Scope tag to filter by (AND logic)
     * @return bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function cleanMatchingAnyTagsWithScope(array $tags, string $scopeTag): bool
    {
        if (empty($tags)) {
            return true;
        }

        // Lua path (if enabled) - atomic operation with scope filtering
        if ($this->useLua && $this->luaHelper && $this->luaHelper->isEnabled()) {
            try {
                $deleted = $this->cleanMatchingAnyTagsWithScopeLua($tags, $scopeTag);

                // Ensure changes are committed
                if (method_exists($this->cachePool, 'commit')) {
                    $this->cachePool->commit();
                }

                return $deleted >= 0; // Lua returns number of items deleted
            // phpcs:disable Magento2.CodeAnalysis.EmptyBlock
            } catch (\Exception $e) {
                // Intentional: Fall through to PHP implementation on Lua failure
            }
            // phpcs:enable Magento2.CodeAnalysis.EmptyBlock
        }

        // Step 1: Get IDs matching ANY of the tags using SUNION (OR logic)
        $anyIds = $this->getIdsMatchingAnyTags($tags);

        if (empty($anyIds)) {
            return true;
        }

        // Step 2: Get IDs matching the scope tag using SMEMBERS
        $scopeIds = $this->redis->sMembers($this->getTagKey($scopeTag));

        if (!is_array($scopeIds) || empty($scopeIds)) {
            return true;
        }

        // Step 3: Intersect to get IDs that have (tag1 OR tag2 OR ...) AND scopeTag
        $filteredIds = array_intersect($anyIds, $scopeIds);

        if (empty($filteredIds)) {
            return true;
        }

        // Step 4: Batch delete filtered IDs
        $success = $this->deleteByIds($filteredIds, array_merge($tags, [$scopeTag]));

        // Step 5: Ensure changes are committed to underlying pool
        if (method_exists($this->cachePool, 'commit')) {
            $this->cachePool->commit();
        }

        return $success;
    }

    /**
     * @inheritDoc
     *
     * Maintains tag-to-ID indices in Redis SETs
     * OPTIMIZED: Uses Redis pipeline for batch operations
     */
    public function onSave(string $id, array $tags, ?int $lifetime = null): void
    {
        if (empty($tags)) {
            return;
        }

        $pipeline = $this->createPipeline();

        // Add ID to all_ids set
        $pipeline->sadd(self::ALL_IDS_SET, $id);

        // Forward index: Add ID to each tag's SET
        foreach ($tags as $tag) {
            $tagKey = $this->getTagKey($tag);
            $pipeline->sadd($tagKey, $id);
        }

        // Reverse index: Store tags for this ID (for cleanup on delete)
        $idTagsKey = $this->reverseIndexKey($id);
        $pipeline->del($idTagsKey);  // Clear old tags first
        foreach ($tags as $tag) {
            $pipeline->sadd($idTagsKey, $tag);
        }

        // Bound the reverse index's lifetime to its data key's. Emitted after the
        // sadd()s so the key exists when EXPIRE runs. Entries saved without a
        // lifetime keep a persistent reverse index, matching their persistent data key.
        if ($lifetime !== null && $lifetime > 0) {
            $pipeline->expire($idTagsKey, $lifetime + self::ID_TAGS_TTL_BUFFER);
        }

        // Execute all operations in one go
        $this->executePipeline($pipeline);
    }

    /**
     * @inheritDoc
     *
     * Removes ID from all tag indices
     * OPTIMIZED: Uses Redis pipeline for batch operations
     */
    public function onRemove(string $id): void
    {
        // Find which tags this ID was associated with store a reverse index: cache:id:tags => SET{tag1, tag2}
        $idTagsKey = $this->reverseIndexKey($id);
        $tags = $this->redis->smembers($idTagsKey);

        if (!is_array($tags) || empty($tags)) {
            // No tags, just remove from all_ids
            $this->redis->srem(self::ALL_IDS_SET, $id);
            return;
        }

        // OPTIMIZATION: Use Redis pipeline for all remove operations, reduces network round trips from N+2 to 1
        $pipeline = $this->createPipeline();

        // Remove from all_ids set
        $pipeline->srem(self::ALL_IDS_SET, $id);

        // Remove ID from each tag's SET in pipeline
        foreach ($tags as $tag) {
            $tagKey = $this->getTagKey($tag);
            $pipeline->srem($tagKey, $id);
        }

        // Delete the reverse index
        $pipeline->del($idTagsKey);

        // Execute all operations in one go
        $this->executePipeline($pipeline);
    }

    /**
     * @inheritDoc
     */
    public function clearAllIndices(): void
    {
        // Use Lua script if enabled for atomic, efficient clearing
        if ($this->useLua && $this->luaHelper) {
            $this->luaHelper->clearAllIndices($this->namespace);
            // Lua script handles everything atomically
            return;
        }

        // Fallback: PHP-based clearing (original implementation)
        // Get all tag keys
        $pattern = self::TAG_INDEX_PREFIX . $this->namespace . '*';
        $tagKeys = $this->redis->keys($pattern);

        if (is_array($tagKeys) && !empty($tagKeys)) {
            // PHP 8+ compatibility: use call_user_func_array to avoid spread operator issues
            call_user_func_array([$this->redis, 'del'], $tagKeys);
        }

        // Clear all_ids set
        $this->redis->del(self::ALL_IDS_SET);

        // Clear reverse index keys
        $reversePattern = self::REVERSE_INDEX_PREFIX . $this->namespace . '*';
        $reverseKeys = $this->redis->keys($reversePattern);
        if (is_array($reverseKeys) && !empty($reverseKeys)) {
            // PHP 8+ compatibility: use call_user_func_array to avoid spread operator issues
            call_user_func_array([$this->redis, 'del'], $reverseKeys);
        }
    }

    /**
     * Store reverse index for efficient onRemove, This should be called after onSave
     *
     * @param string $id
     * @param array $tags
     * @param int|null $lifetime Data-key lifetime; bounds the reverse index TTL when > 0
     * @return void
     */
    public function storeReverseIndex(string $id, array $tags, ?int $lifetime = null): void
    {
        if (empty($tags)) {
            return;
        }

        $idTagsKey = $this->reverseIndexKey($id);

        // OPTIMIZATION: Use Redis pipeline for all operations
        // Reduces network round trips from N+1 to 1
        $pipeline = $this->createPipeline();

        // Clear existing reverse index
        $pipeline->del($idTagsKey);

        // Add all tags to reverse index in pipeline
        foreach ($tags as $tag) {
            $pipeline->sadd($idTagsKey, $tag);
        }

        // Bound the reverse index's lifetime to its data key's (see onSave)
        if ($lifetime !== null && $lifetime > 0) {
            $pipeline->expire($idTagsKey, $lifetime + self::ID_TAGS_TTL_BUFFER);
        }

        // Execute all operations in one go
        $this->executePipeline($pipeline);
    }

    /**
     * Run garbage collection to reap tag bookkeeping orphaned by passive expiry
     *
     * Data keys expire passively (by TTL) with no event the adapter can hook, so their
     * membership in tag sets / all_ids and their reverse index are never cleaned by the
     * normal remove/invalidation paths. This scans all_ids and, for every id whose data
     * key no longer exists, removes its bookkeeping via cleanupIndicesForIds().
     *
     * Bounded by wall time rather than a fixed id count: a fixed cap suits no one
     * (high-volume sites orphan more ids between cron runs than a small cap can drain,
     * so the backlog grows without bound, while small sites don't need a cap at all).
     * The default 2s budget clears tens of thousands of ids per call on typical
     * hardware and finishes early on small sets. An explicit $batchSize additionally
     * caps the number of ids inspected, for callers that need deterministic work.
     *
     * @param int|null $batchSize Maximum ids to inspect per call, or null for time-bounded only
     * @param float $maxRuntime Wall-time budget in seconds
     * @return int Number of orphaned ids cleaned
     */
    public function garbageCollect(?int $batchSize = null, float $maxRuntime = 2.0): int
    {
        if ($batchSize !== null && $batchSize <= 0) {
            return 0;
        }

        $deadline = hrtime(true) + (int)($maxRuntime * 1e9);
        $cleaned = 0;
        $processed = 0;
        $batch = [];

        foreach ($this->scanSet(self::ALL_IDS_SET, min($batchSize ?? 1000, 1000)) as $id) {
            $processed++;
            $batch[] = (string)$id;

            if (count($batch) >= 100) {
                $cleaned += $this->reapOrphanedIds($batch);
                $batch = [];

                if (hrtime(true) >= $deadline) {
                    return $cleaned;
                }
            }

            if ($batchSize !== null && $processed >= $batchSize) {
                break;
            }
        }

        if (!empty($batch)) {
            $cleaned += $this->reapOrphanedIds($batch);
        }

        return $cleaned;
    }

    /**
     * Find which of the given ids have no data key and reap the orphans' bookkeeping
     *
     * Existence is checked with a single batched cachePool->getItems() call (one MGET
     * round trip in Symfony's RedisAdapter) instead of one hasItem() round trip per id.
     * The pool is authoritative on key naming, so this stays correct regardless of how
     * Symfony maps ids to Redis keys.
     *
     * @param array $ids
     * @return int Number of orphaned ids cleaned
     */
    private function reapOrphanedIds(array $ids): int
    {
        $orphans = array_flip($ids);
        foreach ($this->cachePool->getItems($ids) as $key => $item) {
            if ($item->isHit()) {
                unset($orphans[$key]);
            }
        }
        $orphans = array_keys($orphans);

        if (empty($orphans)) {
            return 0;
        }

        // Double-check right before the destructive pass: an id that was missing above
        // may have been re-saved concurrently (popular entries are re-saved just as they
        // expire), and reaping it then would strip a live entry's bookkeeping, leaving
        // it invisible to tag invalidation until its next save. This narrows that window
        // to the confirm->cleanup gap; it cannot close it entirely.
        $confirmed = array_flip($orphans);
        foreach ($this->cachePool->getItems($orphans) as $key => $item) {
            if ($item->isHit()) {
                unset($confirmed[$key]);
            }
        }
        $confirmed = array_keys($confirmed);

        if (empty($confirmed)) {
            return 0;
        }

        $this->cleanupIndicesForIds($confirmed);

        return count($confirmed);
    }

    /**
     * Remove the given ids from the given tags' sets
     *
     * Invalidation discovers ids from these tag sets, but cleanupIndicesForIds() can only
     * SREM memberships still recorded in each id's reverse index. Ids whose reverse index
     * already expired would otherwise stay in the source sets forever; SREMing the fetched
     * ids from them directly makes every tag invalidation self-healing. Specific members
     * are removed (not DEL of the whole set) so concurrently saved ids keep their membership.
     *
     * @param array $tags
     * @param array $ids
     * @return void
     */
    private function removeIdsFromTagSets(array $tags, array $ids): void
    {
        if (empty($tags) || empty($ids)) {
            return;
        }

        $pipeline = $this->createPipeline();
        foreach ($tags as $tag) {
            foreach (array_chunk(array_values($ids), 1000) as $chunk) {
                $pipeline->srem($this->getTagKey($tag), ...$chunk);
            }
        }
        $this->executePipeline($pipeline);
    }

    /**
     * Iterate the members of a Redis SET via SSCAN (cursor-based, non-blocking)
     *
     * Abstracts the phpredis vs Predis SSCAN differences behind a generator.
     *
     * @param string $setKey
     * @param int $count SSCAN COUNT hint per round trip
     * @return \Generator
     */
    private function scanSet(string $setKey, int $count): \Generator
    {
        if ($this->isPredisClient()) {
            $cursor = 0;
            do {
                [$cursor, $members] = $this->redis->sscan($setKey, $cursor, ['COUNT' => $count]);
                if (is_array($members)) {
                    foreach ($members as $member) {
                        yield $member;
                    }
                }
                $cursor = (int)$cursor;
            } while ($cursor !== 0);

            return;
        }

        // phpredis: cursor is passed by reference and reaches 0 when iteration completes
        $iterator = null;
        while (($members = $this->redis->sScan($setKey, $iterator, null, $count)) !== false) {
            if (is_array($members)) {
                foreach ($members as $member) {
                    yield $member;
                }
            }
            if ((int)$iterator === 0) {
                break;
            }
        }
    }

    /**
     * Check if Lua scripts are enabled and available
     *
     * @return bool
     */
    public function isLuaEnabled(): bool
    {
        return ($this->useLua || $this->useLuaOnGc)
            && $this->luaHelper !== null
            && $this->luaHelper->isEnabled();
    }

    /**
     * Clean expired items for specific tag using Lua
     *
     * Only deletes items that have expired (TTL = -2)
     * More efficient than fetching all IDs and checking client-side
     * Uses use_lua flag (general cache operations)
     *
     * @param string $tag Tag to clean
     * @return int Number of items deleted
     */
    public function cleanExpiredByTag(string $tag): int
    {
        // Tag operations check use_lua flag
        if (!$this->useLua || !$this->luaHelper) {
            return 0;
        }

        $tagKey = $this->getTagKey($tag);

        return $this->luaHelper->cleanByTagConditional(
            $tagKey,
            $this->namespace,
            'expired'
        );
    }

    /**
     * Clean cache entries matching ANY tags using Lua script
     *
     * @param array $tags Tags to match (OR logic)
     * @return int Number of items deleted (-1 on error)
     */
    private function cleanMatchingAnyTagsLua(array $tags): int
    {
        if (empty($tags)) {
            return 0;
        }

        // KEYS: array of tags; ARGV: [tag_prefix, namespace, data_prefix, reverse_prefix, all_ids]
        return $this->evalLua(self::LUA_CLEAN_MATCHING_ANY_TAGS, $tags, [
            self::TAG_INDEX_PREFIX,
            $this->namespace,
            $this->dataKeyPrefix(),
            self::REVERSE_INDEX_PREFIX,
            self::ALL_IDS_SET,
        ]);
    }

    /**
     * Execute a Lua script with client-appropriate argument passing
     *
     * The phpredis client expects (script, [keys..., argv...], numKeys); Predis expects
     * (script, numKeys, key1..keyN, arg1..argM) as a flat argument list.
     *
     * Note: eval/evalSha are the Redis EVAL/EVALSHA commands (server-side Lua,
     * class-constant scripts only), not PHP eval().
     *
     * @param string $script
     * @param array $keys
     * @param array $argv
     * @return int Script result (-1 on error, so callers fall back to the PHP path)
     */
    private function evalLua(string $script, array $keys, array $argv): int
    {
        $flat = array_merge(array_values($keys), array_values($argv));

        try {
            $sha = $this->loadLuaScript($script);
            $result = $this->isPredisClient()
                ? $this->redis->evalsha($sha, count($keys), ...$flat)
                : $this->redis->evalSha($sha, $flat, count($keys));

            return (int)$result;
        } catch (\Exception $e) {
            // Fallback: try executing the script directly (e.g. sha evicted by SCRIPT FLUSH)
            try {
                $result = $this->isPredisClient()
                    ? $this->redis->eval($script, count($keys), ...$flat)
                    : $this->redis->eval($script, $flat, count($keys));

                return (int)$result;
            } catch (\Exception $e) {
                // Signal error; callers fall back to the PHP implementation
                return -1;
            }
        }
    }

    /**
     * Get the prefix Symfony's RedisAdapter puts on data keys
     *
     * Symfony appends ':' to a non-empty namespace when building keys
     * (data key = namespace + ':' + id), and uses no prefix for an empty namespace.
     *
     * @return string
     */
    private function dataKeyPrefix(): string
    {
        return $this->namespace === '' ? '' : $this->namespace . ':';
    }

    /**
     * Clean cache entries matching ANY tags within scope using Lua script
     *
     * @param array $tags Tags to match (OR logic)
     * @param string $scopeTag Scope tag to filter by (AND logic)
     * @return int Number of items deleted (-1 on error)
     */
    private function cleanMatchingAnyTagsWithScopeLua(array $tags, string $scopeTag): int
    {
        if (empty($tags)) {
            return 0;
        }

        // KEYS: array of tags
        // ARGV: [tag_prefix, namespace, scope_tag, data_prefix, reverse_prefix, all_ids]
        return $this->evalLua(self::LUA_CLEAN_MATCHING_ANY_TAGS_WITH_SCOPE, $tags, [
            self::TAG_INDEX_PREFIX,
            $this->namespace,
            $scopeTag,
            $this->dataKeyPrefix(),
            self::REVERSE_INDEX_PREFIX,
            self::ALL_IDS_SET,
        ]);
    }

    /**
     * Load Lua script and return SHA1
     *
     * @param string $script Lua script content
     * @return string SHA1 of the script
     * @throws \RedisException
     */
    private function loadLuaScript(string $script): string
    {
        try {
            return $this->redis->script('load', $script);
        } catch (\RedisException $e) {
            throw new \RedisException('Failed to load Lua script: ' . $e->getMessage(), 0, $e);
        }
    }
}
