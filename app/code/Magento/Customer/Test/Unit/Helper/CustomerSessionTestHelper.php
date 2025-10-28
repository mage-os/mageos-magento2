<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Helper;

use Magento\Customer\Model\Session;

/**
 * Test helper for Customer Session with custom methods
 */
class CustomerSessionTestHelper extends Session
{
    /**
     * @var array|null
     */
    private $customerFormData = null;

    /**
     * Constructor that skips parent dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
        // Set storage to prevent session initialization errors
        $this->storage = new \ArrayObject();
    }

    /**
     * Get customer form data (custom method for tests)
     *
     * @return array|null
     */
    public function getCustomerFormData()
    {
        return $this->customerFormData;
    }

    /**
     * Set customer form data
     *
     * @param array|null $data
     * @return $this
     */
    public function setCustomerFormData($data): self
    {
        $this->customerFormData = $data;
        return $this;
    }
}
