<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Helper;

use Magento\Framework\Session\SessionManagerInterface;

/**
 * Test helper for SessionManagerInterface with custom methods
 */
class SessionManagerTestHelper implements SessionManagerInterface
{
    /**
     * @var array<string, mixed>
     */
    private array $testData = [];

    /**
     * Get customer form data
     *
     * @return mixed
     */
    public function getCustomerFormData()
    {
        return $this->testData['customer_form_data'] ?? null;
    }

    /**
     * Set customer form data
     *
     * @param mixed $data
     * @return void
     */
    public function setCustomerFormData($data): void
    {
        $this->testData['customer_form_data'] = $data;
    }

    /**
     * Unset customer form data
     *
     * @return void
     */
    public function unsCustomerFormData(): void
    {
        unset($this->testData['customer_form_data']);
    }

    /**
     * {@inheritdoc}
     */
    public function start()
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function writeClose()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isSessionExists()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($key = '', $clear = false)
    {
        if ($key === '') {
            return $this->testData;
        }
        return $this->testData[$key] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function getSessionId()
    {
        return 'test-session-id';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'test-session';
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function destroy(array $options = null)
    {
        $this->testData = [];
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function clearStorage()
    {
        $this->testData = [];
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCookieDomain()
    {
        return 'localhost';
    }

    /**
     * {@inheritdoc}
     */
    public function getCookiePath()
    {
        return '/';
    }

    /**
     * {@inheritdoc}
     */
    public function getCookieLifetime()
    {
        return 3600;
    }

    /**
     * {@inheritdoc}
     */
    public function setSessionId($sessionId)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSessionIdForHost($urlHost)
    {
        return 'test-session-id';
    }

    /**
     * {@inheritdoc}
     */
    public function isValidForHost($host)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isValidForPath($path)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function regenerateId()
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function expireSessionCookie()
    {
        return $this;
    }
}
