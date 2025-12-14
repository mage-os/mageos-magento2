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
final readonly class RabbitMQConfiguration
{
    public function __construct(
        public bool $enabled,
        public string $host = 'localhost',
        public int $port = 5672,
        public string $user = 'guest',
        #[Sensitive]
        public string $password = 'guest',
        public string $virtualHost = '/'
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
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['enabled'] ?? false,
            $data['host'] ?? 'localhost',
            (int)($data['port'] ?? 5672),
            $data['user'] ?? 'guest',
            $data['password'] ?? 'guest',
            $data['virtualHost'] ?? '/'
        );
    }
}
