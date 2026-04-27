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
     * @param string $hyvaProjectKey
     * @param string $hyvaApiToken
     */
    public function __construct(
        public readonly bool $install,
        public readonly string $theme = '',
        public readonly string $hyvaProjectKey = '',
        public readonly string $hyvaApiToken = ''
    ) {
    }

    /**
     * Convert to array
     *
     * @param bool $includeSensitive Whether to include sensitive fields like API tokens
     * @return array<string, mixed>
     */
    public function toArray(bool $includeSensitive = false): array
    {
        $data = [
            'install' => $this->install,
            'theme' => $this->theme,
            'hyva_project_key' => $this->hyvaProjectKey,
        ];

        if ($includeSensitive) {
            $data['hyva_api_token'] = $this->hyvaApiToken;
        }

        return $data;
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
            $data['theme'] ?? '',
            $data['hyva_project_key'] ?? '',
            $data['hyva_api_token'] ?? ''
        );
    }
}
