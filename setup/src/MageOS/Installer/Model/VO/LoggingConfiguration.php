<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\VO;

/**
 * Logging configuration value object
 */
class LoggingConfiguration
{
    public function __construct(
        public readonly bool $debugMode,
        public readonly string $logLevel
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
            'debugMode' => $this->debugMode,
            'logLevel' => $this->logLevel
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
            $data['debugMode'] ?? false,
            $data['logLevel'] ?? 'error'
        );
    }
}
