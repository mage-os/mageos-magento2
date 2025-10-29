<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Helper;

use Magento\Customer\Model\Address\AbstractAddress;

/**
 * Test helper for AbstractAddress with custom methods
 */
class AbstractAddressTestHelper extends AbstractAddress
{
    /**
     * Constructor that skips parent dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get country ID (custom method for tests)
     *
     * @return string|int|null
     */
    public function getCountryId()
    {
        return null;
    }

    /**
     * Get first name (custom method for tests)
     *
     * @return string|null
     */
    public function getFirstname(): ?string
    {
        return null;
    }

    /**
     * Get last name (custom method for tests)
     *
     * @return string|null
     */
    public function getLastname(): ?string
    {
        return null;
    }

    /**
     * Get city (custom method for tests)
     *
     * @return string|null
     */
    public function getCity(): ?string
    {
        return null;
    }

    /**
     * Get telephone (custom method for tests)
     *
     * @return string|null
     */
    public function getTelephone(): ?string
    {
        return null;
    }

    /**
     * Get fax (custom method for tests)
     *
     * @return string|null
     */
    public function getFax(): ?string
    {
        return null;
    }

    /**
     * Get company (custom method for tests)
     *
     * @return string|null
     */
    public function getCompany(): ?string
    {
        return null;
    }

    /**
     * Get postcode (custom method for tests)
     *
     * @return string|int|null
     */
    public function getPostcode()
    {
        return null;
    }
}

