<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Cache\Test\Unit\Frontend\Adapter\SymfonyAdapters;

/** Records and replays the read-only pipeline used during cleanup. */
class RedisTagAdapterTestPipeline
{
    /** @var RedisTagAdapterTestClient */
    private RedisTagAdapterTestClient $client;

    /** @var array<int, array{0:string,1:array}> */
    private array $queued = [];

    public function __construct(RedisTagAdapterTestClient $client)
    {
        $this->client = $client;
    }

    public function execute(): array
    {
        $results = [];
        foreach ($this->queued as [$method, $args]) {
            $results[] = match ($method) {
                'smembers' => $this->client->sets[$args[0]] ?? [],
                'exists' => 0,
                default => true,
            };
        }

        return $results;
    }

    public function __call($method, $arguments)
    {
        $method = strtolower($method);
        $this->queued[] = [$method, $arguments];
        $this->client->commands[] = [$method, $arguments];

        return $this;
    }
}
