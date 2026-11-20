<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Cache\Test\Unit\Frontend\Adapter\SymfonyAdapters;

use Predis\Client as PredisClient;

/** Predis-shaped test double that records commands without connecting to Redis. */
class RedisTagAdapterTestClient extends PredisClient
{
    /** @var array<int, array{0:string,1:array}> */
    public array $commands = [];

    /** @var array<int, array> */
    public array $rawCommands = [];

    /** @var array<string, array> key => members (SMEMBERS result) */
    public array $sets = [];

    /** @var bool */
    public bool $abortExec = false;

    // phpcs:ignore Magento2.Functions.DiscouragedFunction
    public function __construct()
    {
        // Bypass Predis\Client construction; this double intercepts every call.
    }

    public function executeRaw(array $arguments, &$error = null)
    {
        $this->rawCommands[] = $arguments;
        return 1;
    }

    public function pipeline(...$args)
    {
        return new RedisTagAdapterTestPipeline($this);
    }

    public function __call($method, $arguments)
    {
        $method = strtolower($method);
        $this->commands[] = [$method, $arguments];

        return match ($method) {
            'smembers' => $this->sets[$arguments[0]] ?? [],
            'exec' => $this->abortExec ? false : [1],
            default => true,
        };
    }
}
