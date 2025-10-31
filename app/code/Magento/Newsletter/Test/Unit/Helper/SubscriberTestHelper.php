<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Newsletter\Test\Unit\Helper;

use Magento\Newsletter\Model\Subscriber;

/**
 * Test helper for Subscriber with custom methods
 */
class SubscriberTestHelper extends Subscriber
{
    /**
     * @var \DateTime|string|null
     */
    private $changeStatusAt = null;

    /**
     * @var bool
     */
    private $subscribed = false;

    /**
     * @var array
     */
    private $data = [];

    /**
     * Constructor that skips parent dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Load by customer (mock implementation)
     *
     * @param int $customerId
     * @param int $websiteId
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function loadByCustomer(int $customerId, int $websiteId): \Magento\Newsletter\Model\Subscriber
    {
        return $this;
    }

    /**
     * Get change status at (custom method for tests)
     *
     * @return \DateTime|string|null
     */
    public function getChangeStatusAt()
    {
        return $this->changeStatusAt;
    }

    /**
     * Set change status at
     *
     * @param \DateTime|string|null $date
     * @return $this
     */
    public function setChangeStatusAt($date): self
    {
        $this->changeStatusAt = $date;
        return $this;
    }

    /**
     * Is subscribed
     *
     * @return bool
     */
    public function isSubscribed()
    {
        return $this->subscribed;
    }

    /**
     * Set subscribed
     *
     * @param bool $subscribed
     * @return $this
     */
    public function setSubscribed(bool $subscribed): self
    {
        $this->subscribed = $subscribed;
        return $this;
    }

    /**
     * Get data
     *
     * @param string|null $key
     * @param mixed $index
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getData($key = '', $index = null)
    {
        if ($key === '') {
            return $this->data;
        }
        return $this->data[$key] ?? null;
    }

    /**
     * Set data for testing
     *
     * @param array $testData
     * @return $this
     */
    public function setTestData(array $testData): self
    {
        $this->data = $testData;
        return $this;
    }
}
