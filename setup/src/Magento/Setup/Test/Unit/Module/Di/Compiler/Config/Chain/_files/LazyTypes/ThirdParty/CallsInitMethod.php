<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Vendor\LazyTypesFixture;

/**
 * Mirrors PayPal\Braintree\Model\Adapter\BraintreeAdapter: the constructor sets static SDK state
 */
class CallsInitMethod
{
    /**
     * @var mixed
     */
    private mixed $config;

    public function __construct(mixed $config)
    {
        $this->config = $config;
        $this->initCredentials();
    }

    private function initCredentials(): void
    {
    }
}
