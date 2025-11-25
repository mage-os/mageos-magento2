<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Shipping\Test\Unit\Helper;

use Magento\OfflineShipping\Model\Carrier\Flatrate;

/**
 * Test helper for AbstractCarrierInterface mocking
 *
 * Extends Flatrate which implements both AbstractCarrierInterface and CarrierInterface
 */
class AbstractCarrierInterfaceTestHelper extends Flatrate
{
    /**
     * Skip parent constructor to avoid dependency injection issues
     */
    public function __construct()
    {
        // Skip parent constructor
    }

    /**
     * All methods are inherited from Flatrate
     * No additional methods needed as both isActive() and getAllowedMethods() exist in parent
     */
}


