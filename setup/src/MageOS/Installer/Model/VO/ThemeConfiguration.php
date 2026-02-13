<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\VO;

/**
 * Theme configuration value object
 */
class ThemeConfiguration
{
    /**
     * @param bool $install
     * @param string $theme
     */
    public function __construct(
        public readonly bool $install,
        public readonly string $theme = ''
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
            'install' => $this->install,
            'theme' => $this->theme
        ];
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
            $data['install'] ?? false,
            $data['theme'] ?? ''
        );
    }
}
