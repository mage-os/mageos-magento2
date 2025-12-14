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
class DatabaseConfiguration
{
    /**
     * @param string $host
     * @param string $name
     * @param string $user
     * @param string $password
     * @param string $prefix
     */
    public function __construct(
        public readonly string $host,
        public readonly string $name,
        public readonly string $user,
        #[Sensitive]
        public readonly string $password,
        public readonly string $prefix = ''
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
