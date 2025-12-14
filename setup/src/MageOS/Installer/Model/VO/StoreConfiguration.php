<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\VO;

/**
 * Store configuration value object
 */
final readonly class StoreConfiguration
{
    public function __construct(
        public string $baseUrl,
        public string $language,
        public string $currency,
        public string $timezone,
        public bool $useRewrites
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
            'baseUrl' => $this->baseUrl,
            'language' => $this->language,
            'currency' => $this->currency,
            'timezone' => $this->timezone,
            'useRewrites' => $this->useRewrites
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
            $data['baseUrl'] ?? '',
            $data['language'] ?? 'en_US',
            $data['currency'] ?? 'USD',
            $data['timezone'] ?? 'America/Chicago',
            $data['useRewrites'] ?? true
        );
    }
}
