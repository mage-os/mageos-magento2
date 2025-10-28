<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Helper;

use Magento\Customer\Model\Session;

/**
 * Test helper for Session with custom methods
 */
class SessionTestHelper extends Session
{
    /**
     * @var array|null
     */
    private $addressFormData = null;

    /**
     * @var int|null
     */
    private $customerId = null;

    /**
     * Constructor that skips parent dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get address form data (custom method for tests)
     *
     * @param bool $remove
     * @return array|null
     */
    public function getAddressFormData($remove = false)
    {
        return $this->addressFormData;
    }

    /**
     * Set address form data
     *
     * @param array|null $data
     * @return $this
     */
    public function setAddressFormData($data): self
    {
        $this->addressFormData = $data;
        return $this;
    }

    /**
     * Get customer ID
     *
     * @return int|null
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * Set customer ID
     *
     * @param int|null $id
     * @return $this
     */
    public function setCustomerId($id): self
    {
        $this->customerId = $id;
        return $this;
    }
}
