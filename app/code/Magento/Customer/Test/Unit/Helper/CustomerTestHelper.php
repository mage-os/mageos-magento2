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
}

