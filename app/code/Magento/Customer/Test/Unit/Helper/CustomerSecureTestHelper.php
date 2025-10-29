<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Helper;

use Magento\Customer\Model\Data\CustomerSecure;

/**
 * Test helper for CustomerSecure with custom methods
 */
class CustomerSecureTestHelper extends CustomerSecure
{
    /**
     * @var string|null
     */
    private $rpToken = null;

    /**
     * @var string|null
     */
    private $rpTokenCreatedAt = null;

    /**
     * @var int|null
     */
    private $id = null;

    /**
     * @var string|null
     */
    private $passwordHash = null;

    /**
     * @var bool
     */
    private $isCustomerLocked = false;

    /**
     * @var int
     */
    private $failuresNum = 0;

    /**
     * @var string|null
     */
    private $firstFailure = null;

    /**
     * @var string|null
     */
    private $lockExpires = null;

    /**
     * Constructor that skips parent dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependencies
    }

    /**
     * Set password reset token
     *
     * @param string $token
     * @return $this
     */
    public function setRpToken(string $token): self
    {
        $this->rpToken = $token;
        return $this;
    }

    /**
     * Get password reset token
     *
     * @return string|null
     */
    public function getRpToken(): ?string
    {
        return $this->rpToken;
    }

    /**
     * Set password reset token created at timestamp
     *
     * @param string $createdAt
     * @return $this
     */
    public function setRpTokenCreatedAt(string $createdAt): self
    {
        $this->rpTokenCreatedAt = $createdAt;
        return $this;
    }

    /**
     * Get password reset token created at timestamp
     *
     * @return string|null
     */
    public function getRpTokenCreatedAt(): ?string
    {
        return $this->rpTokenCreatedAt;
    }

    /**
     * Add data to the object
     *
     * @param array $data
     * @return $this
     */
    public function addData(array $data): self
    {
        foreach ($data as $key => $value) {
            $this->setData($key, $value);
        }
        return $this;
    }

    /**
     * Get customer ID
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set customer ID
     *
     * @param int $id
     * @return $this
     */
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get password hash
     *
     * @return string|null
     */
    public function getPasswordHash(): ?string
    {
        return $this->passwordHash;
    }

    /**
     * Set password hash
     *
     * @param string $hash
     * @return $this
     */
    public function setPasswordHash(string $hash): self
    {
        $this->passwordHash = $hash;
        return $this;
    }

    /**
     * Check if customer is locked
     *
     * @return bool
     */
    public function isCustomerLocked(): bool
    {
        return $this->isCustomerLocked;
    }

    /**
     * Set customer locked status
     *
     * @param bool $locked
     * @return $this
     */
    public function setIsCustomerLocked(bool $locked): self
    {
        $this->isCustomerLocked = $locked;
        return $this;
    }

    /**
     * Get failures number
     *
     * @return int
     */
    public function getFailuresNum(): int
    {
        return $this->failuresNum;
    }

    /**
     * Set failures number
     *
     * @param int $num
     * @return $this
     */
    public function setFailuresNum(int $num): self
    {
        $this->failuresNum = $num;
        return $this;
    }

    /**
     * Get first failure timestamp
     *
     * @return string|null
     */
    public function getFirstFailure(): ?string
    {
        return $this->firstFailure;
    }

    /**
     * Set first failure timestamp
     *
     * @param string|null $failure
     * @return $this
     */
    public function setFirstFailure(?string $failure): self
    {
        $this->firstFailure = $failure;
        return $this;
    }

    /**
     * Get lock expires timestamp
     *
     * @return string|null
     */
    public function getLockExpires(): ?string
    {
        return $this->lockExpires;
    }

    /**
     * Set lock expires timestamp
     *
     * @param string|null $expires
     * @return $this
     */
    public function setLockExpires(?string $expires): self
    {
        $this->lockExpires = $expires;
        return $this;
    }
}

