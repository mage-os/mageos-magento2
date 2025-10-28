<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Helper;

use Magento\Customer\Model\Log;

/**
 * Test helper for Log with custom methods
 */
class LogTestHelper extends Log
{
    /**
     * @var string|null
     */
    protected $lastLoginAt = null;

    /**
     * @var string|null
     */
    protected $lastVisitAt = null;

    /**
     * @var string|null
     */
    protected $lastLogoutAt = null;

    /**
     * Constructor that skips parent dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Load by customer (custom method for tests)
     *
     * @param int $customerId
     * @return $this
     */
    public function loadByCustomer($customerId)
    {
        return $this;
    }

    /**
     * Get last login at
     *
     * @return string|null
     */
    public function getLastLoginAt()
    {
        return $this->lastLoginAt;
    }

    /**
     * Set last login at
     *
     * @param string|null $date
     * @return $this
     */
    public function setLastLoginAt($date): self
    {
        $this->lastLoginAt = $date;
        return $this;
    }

    /**
     * Get last visit at
     *
     * @return string|null
     */
    public function getLastVisitAt()
    {
        return $this->lastVisitAt;
    }

    /**
     * Set last visit at
     *
     * @param string|null $date
     * @return $this
     */
    public function setLastVisitAt($date): self
    {
        $this->lastVisitAt = $date;
        return $this;
    }

    /**
     * Get last logout at
     *
     * @return string|null
     */
    public function getLastLogoutAt()
    {
        return $this->lastLogoutAt;
    }

    /**
     * Set last logout at
     *
     * @param string|null $date
     * @return $this
     */
    public function setLastLogoutAt($date): self
    {
        $this->lastLogoutAt = $date;
        return $this;
    }
}
