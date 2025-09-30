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
    public function __construct()
    {
    }

    public function setStores($stores)
    {
        return $this;
    }
}


