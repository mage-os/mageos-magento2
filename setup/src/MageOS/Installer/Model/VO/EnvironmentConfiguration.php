<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\VO;

/**
 * Environment configuration value object
 */
final readonly class EnvironmentConfiguration
{
    public function __construct(
        public string $type,
        public string $mageMode
    ) {
    }

    /**
     * Is development environment?
     *
     * @return bool
     */
    public function isDevelopment(): bool
    {
        return $this->type === 'development';
    }

    /**
     * Is production environment?
     *
     * @return bool
     */
    public function isProduction(): bool
    {
        return $this->type === 'production';
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
            'type' => $this->type,
            'mageMode' => $this->mageMode
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
            $data['type'] ?? 'development',
            $data['mageMode'] ?? 'developer'
        );
    }
}
