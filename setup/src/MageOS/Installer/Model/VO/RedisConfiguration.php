<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\VO;

/**
 * Redis configuration value object
 */
final readonly class RedisConfiguration
{
    public function __construct(
        public bool $session,
        public bool $cache,
        public bool $fpc,
        public string $host = '127.0.0.1',
        public int $port = 6379,
        public int $sessionDb = 0,
        public int $cacheDb = 1,
        public int $fpcDb = 2
    ) {
    }

    /**
     * Is any Redis feature enabled?
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->session || $this->cache || $this->fpc;
    }

    /**
     * Convert to array
     *
     * @param bool $includeSensitive Whether to include sensitive fields (none here)
     * @return array<string, mixed>
     */
    public function toArray(bool $includeSensitive = false): array
    {
        return [
            'session' => $this->session,
            'cache' => $this->cache,
            'fpc' => $this->fpc,
            'host' => $this->host,
            'port' => $this->port,
            'sessionDb' => $this->sessionDb,
            'cacheDb' => $this->cacheDb,
            'fpcDb' => $this->fpcDb
        ];
    }

    /**
     * Create from array
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['session'] ?? false,
            $data['cache'] ?? false,
            $data['fpc'] ?? false,
            $data['host'] ?? '127.0.0.1',
            (int)($data['port'] ?? 6379),
            (int)($data['sessionDb'] ?? 0),
            (int)($data['cacheDb'] ?? 1),
            (int)($data['fpcDb'] ?? 2)
        );
    }
}
