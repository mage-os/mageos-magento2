<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\VO;

use MageOS\Installer\Model\VO\Attribute\Sensitive;

/**
 * Database configuration value object
 */
final readonly class DatabaseConfiguration
{
    public function __construct(
        public string $host,
        public string $name,
        public string $user,
        #[Sensitive]
        public string $password,
        public string $prefix = ''
    ) {
    }

    /**
     * Convert to array
     *
     * @param bool $includeSensitive Whether to include sensitive fields
     * @return array<string, string>
     */
    public function toArray(bool $includeSensitive = false): array
    {
        $data = [
            'host' => $this->host,
            'name' => $this->name,
            'user' => $this->user,
            'prefix' => $this->prefix
        ];

        if ($includeSensitive) {
            $data['password'] = $this->password;
        }

        return $data;
    }

    /**
     * Create from array
     *
     * @param array<string, string> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['host'] ?? '',
            $data['name'] ?? '',
            $data['user'] ?? '',
            $data['password'] ?? '',
            $data['prefix'] ?? ''
        );
    }
}
