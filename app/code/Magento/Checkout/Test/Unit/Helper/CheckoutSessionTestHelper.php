<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Checkout\Test\Unit\Helper;

use Magento\Checkout\Model\Session;

/**
 * Test helper for Checkout Session to expose last order setters for tests.
 */
class CheckoutSessionTestHelper extends Session
{
    /** @var array */
    private array $testData = [];

    /**
     * Constructor intentionally empty to skip parent dependencies.
     */
    public function __construct()
    {
    }

    /**
     * @param mixed $id
     * @return $this
     */
    public function setLastQuoteId($id)
    {
        $this->testData['last_quote_id'] = $id;
        return $this;
    }

    /**
     * @param mixed $id
     * @return $this
     */
    public function setLastSuccessQuoteId($id)
    {
        $this->testData['last_success_quote_id'] = $id;
        return $this;
    }

    /**
     * @param mixed $id
     * @return $this
     */
    public function setLastOrderId($id)
    {
        $this->testData['last_order_id'] = $id;
        return $this;
    }

    /**
     * @param mixed $id
     * @return $this
     */
    public function setLastRealOrderId($id)
    {
        $this->testData['last_real_order_id'] = $id;
        return $this;
    }

    /**
     * @param mixed $status
     * @return $this
     */
    public function setLastOrderStatus($status)
    {
        $this->testData['last_order_status'] = $status;
        return $this;
    }
}
