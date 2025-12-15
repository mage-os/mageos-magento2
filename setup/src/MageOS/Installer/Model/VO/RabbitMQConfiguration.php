<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\VO;

use MageOS\Installer\Model\VO\Attribute\Sensitive;

/**
 * RabbitMQ configuration value object
 */
class RabbitMQConfiguration
{
    public function __construct(
        public readonly bool $enabled,
        public readonly string $host = 'localhost',
        public readonly int $port = 5672,
        public readonly string $user = 'guest',
        #[Sensitive]
        public readonly string $password = 'guest',
        public readonly string $virtualHost = '/'
    ) {
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
            'enabled' => $this->enabled,
            'host' => $this->host,
            'port' => $this->port,
            'user' => $this->user,
            'virtualHost' => $this->virtualHost
        ];

        if ($includeSensitive) {
            $data['password'] = $this->password;
        }

        return $data;
    }

    /**
     * Create from array
     *
     * @param array<string, mixed>|null $data
     * @return self
     */
    public static function fromArray(?array $data): self
    {
        if ($data === null) {
            // Not configured - return disabled
            return new self(false);
        }

        return new self(
            $data['enabled'] ?? false,
            $data['host'] ?? 'localhost',
            (int)($data['port'] ?? 5672),
            $data['user'] ?? 'guest',
            $data['password'] ?? 'guest',
            $data['virtualHost'] ?? $data['virtualhost'] ?? '/' // Handle both formats
        );
    }
}
