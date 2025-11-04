<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Helper;

use Magento\Framework\Event\Observer;

/**
 * Test helper for Observer to support custom methods
 */
class ObserverTestHelper extends Observer
{
    /**
     * @var array
     */
    private array $data = [];

    /**
     * Constructor - skip parent
     */
    public function __construct()
    {
        // Skip parent constructor
    }

    /**
     * Get customer address
     *
     * @return mixed
     */
    public function getCustomerAddress()
    {
        return $this->data['customer_address'] ?? null;
    }

    /**
     * Set customer address
     *
     * @param mixed $address
     * @return $this
     */
    public function setCustomerAddress($address): self
    {
        $this->data['customer_address'] = $address;
        return $this;
    }
}
