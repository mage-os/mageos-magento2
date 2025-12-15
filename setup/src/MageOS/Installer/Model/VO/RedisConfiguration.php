<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\VO;

/**
 * Redis configuration value object
 */
class RedisConfiguration
{
    public function __construct(
        public readonly bool $session,
        public readonly bool $cache,
        public readonly bool $fpc,
        public readonly string $host = '127.0.0.1',
        public readonly int $port = 6379,
        public readonly int $sessionDb = 0,
        public readonly int $cacheDb = 1,
        public readonly int $fpcDb = 2
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
     * Handles both flat format (from saved config) and nested format (from RedisConfig::collect())
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        // Check if this is the nested format from RedisConfig::collect()
        if (isset($data['session']) && is_array($data['session'])) {
            // Nested format with separate session/cache/fpc arrays
            $sessionEnabled = isset($data['session']['enabled']) && $data['session']['enabled'];
            $cacheEnabled = isset($data['cache']['enabled']) && $data['cache']['enabled'];
            $fpcEnabled = isset($data['fpc']['enabled']) && $data['fpc']['enabled'];

            $host = $data['session']['host'] ?? $data['cache']['host'] ?? $data['fpc']['host'] ?? '127.0.0.1';
            $port = (int)($data['session']['port'] ?? $data['cache']['port'] ?? $data['fpc']['port'] ?? 6379);
            $sessionDb = (int)($data['session']['database'] ?? 0);
            $cacheDb = (int)($data['cache']['database'] ?? 1);
            $fpcDb = (int)($data['fpc']['database'] ?? 2);

            return new self(
                $sessionEnabled,
                $cacheEnabled,
                $fpcEnabled,
                $host,
                $port,
                $sessionDb,
                $cacheDb,
                $fpcDb
            );
        }

        // Flat format from saved config
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
