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
     * @var string|null
     */
    private $username = null;

    /**
     * @var int|null
     */
    private $lastCustomerId = null;

    /**
     * @var array|null
     */
    private $addressFormData = null;

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

    /**
     * Set username (custom method for tests)
     *
     * @param string $username
     * @return $this
     */
    public function setUsername(string $username): self
    {
        $this->username = $username;
        return $this;
    }

    /**
     * Get username
     *
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * Set last customer ID (custom method for tests)
     *
     * @param int $customerId
     * @return $this
     */
    public function setLastCustomerId(int $customerId): self
    {
        $this->lastCustomerId = $customerId;
        return $this;
    }

    /**
     * Get last customer ID
     *
     * @return int|null
     */
    public function getLastCustomerId(): ?int
    {
        return $this->lastCustomerId;
    }

    /**
     * Set address form data (custom method for tests)
     *
     * @param string|array|null $data
     * @return $this
     */
    public function setAddressFormData(string|array|null $data): self
    {
        $this->addressFormData = is_array($data) ? $data : [$data];
        return $this;
    }

    /**
     * Get address form data
     *
     * @return array|null
     */
    public function getAddressFormData(): ?array
    {
        return $this->addressFormData;
    }

    /**
     * Get before auth URL
     *
     * @return string|null
     */
    public function getBeforeAuthUrl(): ?string
    {
        return $this->getData('before_auth_url');
    }

    /**
     * Set no referer (custom method for tests)
     *
     * @param bool $flag
     * @return $this
     */
    public function setNoReferer(bool $flag = true): self
    {
        $this->setData('no_referer', $flag);
        return $this;
    }

    /**
     * Unset no referer (custom method for tests)
     *
     * @return $this
     */
    public function unsNoReferer(): self
    {
        $this->unsetData('no_referer');
        return $this;
    }
}
