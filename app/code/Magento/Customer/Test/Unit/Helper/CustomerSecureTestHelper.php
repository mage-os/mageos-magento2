<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
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
     * @var int|string
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
     * @param string|null $token
     * @return $this
     */
    public function setRpToken($token): self
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
     * @param string|null $createdAt
     * @return $this
     */
    public function setRpTokenCreatedAt($createdAt): self
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
     * @param string|null $hash
     * @return $this
     */
    public function setPasswordHash($hash): self
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
     * @return int|string
     */
    public function getFailuresNum()
    {
        return $this->failuresNum;
    }

    /**
     * Set failures number
     *
     * @param int|string $num
     * @return $this
     */
    public function setFailuresNum($num): self
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
