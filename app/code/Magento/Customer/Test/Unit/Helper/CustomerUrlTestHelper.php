<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Helper;

use Magento\Customer\Model\Url as CustomerUrl;

/**
 * Test helper for Customer\Model\Url with custom methods
 */
class CustomerUrlTestHelper extends CustomerUrl
{
    /**
     * Constructor that skips parent dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get account URL (override for tests)
     *
     * @return string
     */
    public function getAccountUrl()
    {
        return '';
    }

    /**
     * Get login URL (override for tests)
     *
     * @return string
     */
    public function getLoginUrl()
    {
        return '';
    }

    /**
     * Get logout URL (override for tests)
     *
     * @return string
     */
    public function getLogoutUrl()
    {
        return '';
    }

    /**
     * Get dashboard URL (override for tests)
     *
     * @return string
     */
    public function getDashboardUrl()
    {
        return '';
    }
}

