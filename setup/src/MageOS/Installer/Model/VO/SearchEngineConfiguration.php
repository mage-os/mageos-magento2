<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\VO;

use MageOS\Installer\Model\VO\Attribute\Sensitive;

/**
 * Search engine configuration value object
 */
class SearchEngineConfiguration
{
    /**
     * @param string $engine
     * @param string $host
     * @param int $port
     * @param string $prefix
     * @param bool $enableAuth
     * @param string $username
     * @param string $password
     */
    public function __construct(
        public readonly string $engine,
        public readonly string $host,
        public readonly int $port,
        public readonly string $prefix = '',
        public readonly bool $enableAuth = false,
        public readonly string $username = '',
        #[Sensitive]
        public readonly string $password = ''
    ) {
    }

    /**
     * Get host with port
     *
     * @return string
     */
    public function getHostWithPort(): string
    {
        return sprintf('%s:%d', $this->host, $this->port);
    }

    /**
     * Is OpenSearch?
     *
     * @return bool
     */
    public function isOpenSearch(): bool
    {
        return $this->engine === 'opensearch';
    }

    /**
     * Is Elasticsearch?
     *
     * @return bool
     */
    public function isElasticsearch(): bool
    {
        return str_starts_with($this->engine, 'elasticsearch');
    }

    /**
     * Convert to array
     *
     * @param bool $includeSensitive Whether to include sensitive fields
     * @return array<string, mixed>
     */
    public function toArray(bool $includeSensitive = false): array
    {
        $data = [
            'engine' => $this->engine,
            'host' => $this->host,
            'port' => $this->port,
            'prefix' => $this->prefix,
            'enableAuth' => $this->enableAuth,
            'username' => $this->username
        ];

        if ($includeSensitive) {
            $data['password'] = $this->password;
        }

        return $data;
    }

    /**
     * Create from array
     *
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['engine'] ?? 'opensearch',
            $data['host'] ?? 'localhost',
            (int)($data['port'] ?? 9200),
            $data['prefix'] ?? '',
            (bool)($data['enableAuth'] ?? false),
            $data['username'] ?? '',
            $data['password'] ?? ''
        );
    }
}
