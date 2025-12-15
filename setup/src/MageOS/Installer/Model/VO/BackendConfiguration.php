<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\VO;

/**
 * Backend configuration value object
 */
class BackendConfiguration
{
    public function __construct(
        public readonly string $frontname
    ) {
    }

    /**
     * Convert to array
     *
     * @param bool $includeSensitive Whether to include sensitive fields (none here)
     * @return array<string, string>
     */
    public function toArray(bool $includeSensitive = false): array
    {
        return [
            'frontname' => $this->frontname
        ];
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
            $data['frontname'] ?? 'admin'
        );
    }
}
