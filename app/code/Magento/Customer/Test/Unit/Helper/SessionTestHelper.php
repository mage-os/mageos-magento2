<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Helper;

use Magento\Customer\Model\Session;

class SessionTestHelper extends Session
{
    public function __construct()
    {
        // Empty constructor
    }

    /**
     * @return array
     */
    public function getDefaultTaxBillingAddress()
    {
        return ['billing_address'];
    }

    /**
     * @return array
     */
    public function getDefaultTaxShippingAddress()
    {
        return ['shipping_address'];
    }

    /**
     * @return int
     */
    public function getCustomerTaxClassId()
    {
        return 3;
    }

    /**
     * @return int
     */
    public function getCustomerGroupId()
    {
        return 2;
    }

    /**
     * @return int
     */
    public function getCustomerId()
    {
        return 4;
    }
}

