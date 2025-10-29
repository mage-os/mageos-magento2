<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Helper;

use Magento\Customer\Model\Customer;

/**
 * Test helper for Customer model with custom methods
 */
class CustomerTestHelper extends Customer
{
    /**
     * @var int|null
     */
    private $storeId = null;

    /**
     * @var string|null
     */
    private $email = null;

    /**
     * @var int|null
     */
    private $websiteId = null;

    /**
     * Constructor that skips parent dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get store ID (custom method for tests)
     *
     * @return int|null
     */
    public function getStoreId(): ?int
    {
        return $this->storeId;
    }

    /**
     * Set store ID
     *
     * @param int|null $storeId
     * @return $this
     */
    public function setTestStoreId(?int $storeId): self
    {
        $this->storeId = $storeId;
        return $this;
    }

    /**
     * Get email (custom method for tests)
     *
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Set email (custom method for tests)
     *
     * @param string|null $email
     * @return $this
     */
    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Get website ID (custom method for tests)
     *
     * @return int|null
     */
    public function getWebsiteId(): ?int
    {
        return $this->websiteId;
    }

    /**
     * Set website ID (custom method for tests)
     *
     * @param int|null $websiteId
     * @return $this
     */
    public function setWebsiteId(?int $websiteId): self
    {
        $this->websiteId = $websiteId;
        return $this;
    }

    /**
     * Load by email (stub for tests)
     *
     * @param string $customerEmail
     * @return $this
     */
    public function loadByEmail($customerEmail)
    {
        // Stub implementation for tests
        return $this;
    }
}

