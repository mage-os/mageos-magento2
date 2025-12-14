<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\VO;

/**
 * Sample data configuration value object
 */
class SampleDataConfiguration
{
    public function __construct(
        public readonly bool $install
    ) {
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
            'install' => $this->install
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
            $data['install'] ?? false
        );
    }
}
