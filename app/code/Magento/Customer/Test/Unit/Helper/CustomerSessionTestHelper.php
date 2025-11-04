<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */

declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Helper;

use Magento\Customer\Model\Session;

/**
 * Test helper for Customer Session with custom methods
 * @SuppressWarnings(PHPMD.UnusedPrivateField)
 * @SuppressWarnings(PHPMD.ShortVariable)
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
     * @var array|null
     */
    private $visitorData = null;

    /**
     * @var string|null
     */
    private $sessionId = null;

    /**
     * @var string|null
     */
    private $beforeAuthUrl = null;

    /**
     * @var string|null
     */
    private $afterAuthUrl = null;

    /**
     * @var array|null
     */
    private $beforeRequestParams = null;

    /**
     * @var string|null
     */
    private $beforeModuleName = null;

    /**
     * @var string|null
     */
    private $beforeControllerName = null;

    /**
     * @var string|null
     */
    private $beforeAction = null;

    /**
     * @var bool
     */
    private $isLoggedIn = false;

    /**
     * @var int|null
     */
    private $id = null;

    /**
     * Constructor that skips parent dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
        // Set storage to prevent session initialization errors
        // phpcs:ignore Magento2.Legacy.RestrictedCode.ArrayObjectIsRestricted
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
     * Get no referer (custom method for tests)
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getNoReferer(): bool
    {
        return (bool)$this->getData('no_referer');
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

    /**
     * Get visitor data (custom method for tests)
     *
     * @return array|null
     */
    public function getVisitorData(): ?array
    {
        return $this->visitorData;
    }

    /**
     * Set visitor data (custom method for tests)
     *
     * @param array|null $data
     * @return $this
     */
    public function setVisitorData(?array $data): self
    {
        $this->visitorData = $data;
        return $this;
    }

    /**
     * Get session ID (custom method for tests)
     *
     * @return string|null
     */
    public function getSessionId(): ?string
    {
        return $this->sessionId;
    }

    /**
     * Set session ID (override parent method for tests)
     *
     * @param string $sessionId
     * @return $this
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;
        return $this;
    }

    /**
     * Set before auth URL (override parent for tests)
     *
     * @param string $url
     * @return $this
     */
    public function setBeforeAuthUrl($url)
    {
        $this->setData('before_auth_url', $url);
        return $this;
    }

    /**
     * Unset before auth URL (custom method for tests)
     *
     * @return $this
     */
    public function unsBeforeAuthUrl(): self
    {
        $this->unsetData('before_auth_url');
        return $this;
    }

    /**
     * Get after auth URL (custom method for tests)
     *
     * @return string|null
     */
    public function getAfterAuthUrl(): ?string
    {
        return $this->afterAuthUrl;
    }

    /**
     * Set after auth URL (override parent for tests)
     *
     * @param string $url
     * @return $this
     */
    public function setAfterAuthUrl($url)
    {
        $this->afterAuthUrl = $url;
        return $this;
    }

    /**
     * Get before request params (custom method for tests)
     *
     * @return array|bool|null
     */
    public function getBeforeRequestParams()
    {
        return $this->beforeRequestParams;
    }

    /**
     * Set before request params (for test setup)
     *
     * @param array|null $params
     * @return $this
     */
    public function setBeforeRequestParams(?array $params): self
    {
        $this->beforeRequestParams = $params;
        return $this;
    }

    /**
     * Get before module name (custom method for tests)
     *
     * @return string|null
     */
    public function getBeforeModuleName(): ?string
    {
        return $this->beforeModuleName;
    }

    /**
     * Set before module name (for test setup)
     *
     * @param string|null $moduleName
     * @return $this
     */
    public function setBeforeModuleName(?string $moduleName): self
    {
        $this->beforeModuleName = $moduleName;
        return $this;
    }

    /**
     * Get before controller name (custom method for tests)
     *
     * @return string|null
     */
    public function getBeforeControllerName(): ?string
    {
        return $this->beforeControllerName;
    }

    /**
     * Set before controller name (for test setup)
     *
     * @param string|null $controllerName
     * @return $this
     */
    public function setBeforeControllerName(?string $controllerName): self
    {
        $this->beforeControllerName = $controllerName;
        return $this;
    }

    /**
     * Get before action (custom method for tests)
     *
     * @return string|null
     */
    public function getBeforeAction(): ?string
    {
        return $this->beforeAction;
    }

    /**
     * Set before action (for test setup)
     *
     * @param string|null $action
     * @return $this
     */
    public function setBeforeAction(?string $action): self
    {
        $this->beforeAction = $action;
        return $this;
    }

    /**
     * Check if customer is logged in (override parent for tests)
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->isLoggedIn;
    }

    /**
     * Set logged in flag (for test setup)
     *
     * @param bool $isLoggedIn
     * @return $this
     */
    public function setIsLoggedIn(bool $isLoggedIn): self
    {
        $this->isLoggedIn = $isLoggedIn;
        return $this;
    }

    /**
     * Get customer ID (override parent for tests)
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set customer ID (override parent for tests)
     *
     * @param int|null $customerId
     * @return $this
     */
    public function setId($customerId)
    {
        $this->id = $customerId;
        return $this;
    }
}
