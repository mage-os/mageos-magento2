<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CheckoutAgreements\Test\Unit\Helper;

use Magento\CheckoutAgreements\Model\Agreement as AgreementModel;

/**
 * Agreement model stub exposing setStores for mocking in tests.
 */
class AgreementModelSetStoresTestHelper extends AgreementModel
{
    /**
     * @var mixed
     */
    private $storesValue;

    /**
     * Create lightweight Agreement model without DI for tests.
     */
    public function __construct()
    {
    }

    /**
     * Set stores value for testing and return self.
     *
     * @param mixed $stores
     * @return $this
     */
    public function setStores($stores)
    {
        $this->storesValue = $stores;
        return $this;
    }
}


