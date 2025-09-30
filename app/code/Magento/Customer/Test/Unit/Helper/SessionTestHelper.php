<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Helper;

use Magento\Customer\Model\Session;

/**
 * Test helper class for Customer Session used across Customer and related module tests
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class SessionTestHelper extends Session
{
    /**
     * @var bool
     */
    public bool $loggedIn = false;

    /**
     * @var int
     */
    public int $customerId = 1;

    /**
     * @var int
     */
    public int $wishlistItemCount = 0;

    /**
     * @var mixed
     */
    private $defaultTaxShippingAddress = null;

    /**
     * @var mixed
     */
    private $defaultTaxBillingAddress = null;

    /**
     * @var mixed
     */
    private $customerTaxClassId = null;

    /**
     * @var mixed
     */
    private $websiteId = null;

    /**
     * @var mixed
     */
    public $beforeWishlistUrl = null;

    /**
     * @var mixed
     */
    public $beforeWishlistRequest = null;

    /**
     * @var mixed
     */
    private $urlFactory = null;

    /**
     * @var mixed
     */
    private $customerFactory = null;

    /**
     * @var mixed
     */
    public $_urlFactory = null;

    /**
     * @var mixed
     */
    public $_customerFactory = null;

    /**
     * @var mixed
     */
    public $storage = null;

    /**
     * @var mixed
     */
    public $_customerUrl = null;

    /**
     * @var mixed
     */
    public $response = null;

    /**
     * @var mixed
     */
    public $groupManagement = null;

    /**
     * Constructor - skip parent constructor to avoid dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
        // Set up basic mock for groupManagement to prevent integration test failures
        $this->groupManagement = $this->createMockGroupManagement();
        // Set up basic mock for storage to prevent integration test failures
        $this->storage = $this->createMockStorage();
    }

    /**
     * Create a mock group management object
     *
     * @return object
     */
    private function createMockGroupManagement()
    {
        return new class {
            public function getNotLoggedInGroup()
            {
                return new class {
                    public function getId()
                    {
                        return 0;
                    }
                };
            }
        };
    }

    /**
     * Create a mock storage object
     *
     * @return object
     */
    private function createMockStorage()
    {
        return new class {
            /**
             * @var array
             */
            private $data = [];

            public function setData($key, $dataValue = null)
            {
                if ($dataValue !== null) {
                    $this->data[$key] = $dataValue;
                }
                return $this;
            }

            public function getData($key)
            {
                return $this->data[$key] ?? null;
            }
        };
    }

    /**
     * Check if customer is logged in
     *
     * @return bool
     */
    public function isLoggedIn(): bool
    {
        return $this->loggedIn;
    }

    /**
     * Set logged in status
     *
     * @param bool $loggedIn
     * @return $this
     */
    public function setIsLoggedIn(bool $loggedIn): self
    {
        $this->loggedIn = $loggedIn;
        return $this;
    }

    /**
     * Get customer ID
     *
     * @return int
     */
    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    /**
     * Set customer ID
     *
     * @param mixed $customerId
     * @return $this
     */
    public function setCustomerId($customerId): self
    {
        $this->customerId = (int)$customerId;
        return $this;
    }

    /**
     * Set wishlist item count
     *
     * @param int $count
     * @return $this
     */
    public function setWishlistItemCount(int $count): self
    {
        $this->wishlistItemCount = $count;
        return $this;
    }

    /**
     * Get wishlist item count
     *
     * @return int
     */
    public function getWishlistItemCount(): int
    {
        return $this->wishlistItemCount;
    }

    /**
     * @var mixed
     */
    private $customer = null;

    /**
     * Get customer
     *
     * @return mixed
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Set customer
     *
     * @param mixed $customer
     * @return $this
     */
    public function setCustomer($customer): self
    {
        $this->customer = $customer;
        return $this;
    }

    /**
     * Get data
     *
     * @param string $key
     * @param bool $clear
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getData($key = '', $clear = false)
    {
        return false;
    }

    /**
     * Get default tax shipping address
     *
     * @return mixed
     */
    public function getDefaultTaxShippingAddress()
    {
        return $this->defaultTaxShippingAddress;
    }

    /**
     * Set default tax shipping address
     *
     * @param mixed $address
     * @return $this
     */
    public function setDefaultTaxShippingAddress($address)
    {
        $this->defaultTaxShippingAddress = $address;
        return $this;
    }

    /**
     * Get default tax billing address
     *
     * @return mixed
     */
    public function getDefaultTaxBillingAddress()
    {
        return $this->defaultTaxBillingAddress;
    }

    /**
     * Set default tax billing address
     *
     * @param mixed $address
     * @return $this
     */
    public function setDefaultTaxBillingAddress($address)
    {
        $this->defaultTaxBillingAddress = $address;
        return $this;
    }

    /**
     * Get customer tax class ID
     *
     * @return mixed
     */
    public function getCustomerTaxClassId()
    {
        return $this->customerTaxClassId;
    }

    /**
     * Set customer tax class ID
     *
     * @param mixed $id
     * @return $this
     */
    public function setCustomerTaxClassId($id)
    {
        $this->customerTaxClassId = $id;
        return $this;
    }

    /**
     * Get website ID
     *
     * @return mixed
     */
    public function getWebsiteId()
    {
        return $this->websiteId;
    }

    /**
     * Set website ID
     *
     * @param mixed $id
     * @return $this
     */
    public function setWebsiteId($id)
    {
        $this->websiteId = $id;
        return $this;
    }

    /**
     * Set URL factory
     *
     * @param mixed $urlFactory
     * @return $this
     */
    public function setUrlFactory($urlFactory): self
    {
        $this->urlFactory = $urlFactory;
        return $this;
    }

    /**
     * Get URL factory
     *
     * @return mixed
     */
    public function getUrlFactory()
    {
        return $this->urlFactory;
    }

    /**
     * Set customer factory
     *
     * @param mixed $customerFactory
     * @return $this
     */
    public function setCustomerFactory($customerFactory): self
    {
        $this->customerFactory = $customerFactory;
        return $this;
    }

    /**
     * Get customer factory
     *
     * @return mixed
     */
    public function getCustomerFactory()
    {
        return $this->customerFactory;
    }

    /**
     * Set data
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function setData($key, $value = null)
    {
        if ($this->storage) {
            $this->storage->setData($key, $value);
        }
        return $this;
    }

    /**
     * Get customer group ID
     *
     * @return int
     */
    public function getCustomerGroupId()
    {
        if ($this->storage) {
            return $this->storage->getData('customer_group_id') ?? 0;
        }
        return 0;
    }

    /**
     * Set customer group ID
     *
     * @param int $groupId
     * @return $this
     */
    public function setCustomerGroupId($groupId)
    {
        if ($this->storage) {
            $this->storage->setData('customer_group_id', $groupId);
        }
        return $this;
    }

    /**
     * Get customer data
     *
     * @return mixed
     */
    public function getCustomerData()
    {
        return $this->customer;
    }

    /**
     * Check customer ID
     *
     * @param int $customerId
     * @return bool
     */
    public function checkCustomerId($customerId)
    {
        return $customerId > 0;
    }

    /**
     * Get ID
     *
     * @return int
     */
    public function getId()
    {
        return $this->customerId;
    }

    /**
     * Check if customer is emulated
     *
     * @return bool
     */
    public function isCustomerEmulated()
    {
        return false;
    }

    /**
     * Get is customer emulated (alias for compatibility)
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsCustomerEmulated()
    {
        return $this->isCustomerEmulated();
    }

    /**
     * Reset state after request
     *
     * @return void
     */
    public function _resetState(): void
    {
        // Reset all properties to their default values
        $this->loggedIn = false;
        $this->customerId = 1;
        $this->wishlistItemCount = 0;
        $this->defaultTaxShippingAddress = null;
        $this->defaultTaxBillingAddress = null;
        $this->customerTaxClassId = null;
        $this->websiteId = null;
        $this->beforeWishlistUrl = null;
        $this->beforeWishlistRequest = null;
        $this->customer = null;
        $this->urlFactory = null;
        $this->customerFactory = null;
        $this->_urlFactory = null;
        $this->_customerFactory = null;
        $this->_customerUrl = null;
        $this->response = null;
        
        // Reset storage data if available
        if ($this->storage) {
            $this->storage->setData('customer_group_id', null);
        }
    }
}
