<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\VO;

use MageOS\Installer\Model\VO\Attribute\Sensitive;

/**
 * Email configuration value object
 */
final readonly class EmailConfiguration
{
    public function __construct(
        public bool $configure,
        public string $transport = 'sendmail',
        public string $host = '',
        public int $port = 587,
        public string $auth = '',
        public string $username = '',
        #[Sensitive]
        public string $password = ''
    ) {
    }

    /**
     * Is SMTP transport?
     *
     * @return bool
     */
    public function isSmtp(): bool
    {
        return $this->transport === 'smtp';
    }

    /**
     * Convert to array
     *
     * @param bool $includeSensitive Whether to include sensitive fields
     * @return array<string, mixed>
     */
    public function toArray(bool $includeSensitive = false): array
    {
        $data = [
            'configure' => $this->configure,
            'transport' => $this->transport,
            'host' => $this->host,
            'port' => $this->port,
            'auth' => $this->auth,
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
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['configure'] ?? false,
            $data['transport'] ?? 'sendmail',
            $data['host'] ?? '',
            (int)($data['port'] ?? 587),
            $data['auth'] ?? '',
            $data['username'] ?? '',
            $data['password'] ?? ''
        );
    }
}
