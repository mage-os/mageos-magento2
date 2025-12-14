<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\VO;

use MageOS\Installer\Model\VO\Attribute\Sensitive;

/**
 * Admin account configuration value object
 */
final readonly class AdminConfiguration
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $email,
        public string $username,
        #[Sensitive]
        public string $password
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
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'email' => $this->email,
            'username' => $this->username
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
            $data['firstName'] ?? '',
            $data['lastName'] ?? '',
            $data['email'] ?? '',
            $data['username'] ?? '',
            $data['password'] ?? ''
        );
    }
}
