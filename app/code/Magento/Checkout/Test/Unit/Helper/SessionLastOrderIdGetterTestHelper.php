<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Checkout\Test\Unit\Helper;

use Magento\Checkout\Model\Session;

/**
 * Test helper that exposes a concrete getLastOrderId() for \Magento\Checkout\Model\Session.
 */
class SessionLastOrderIdGetterTestHelper extends Session
{
    /** @var int|string|null */
    private $id;

    /**
     * @param int|string|null $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Returns last order id set via constructor for test purposes.
     *
     * @return int|string|null
     */
    public function getLastOrderId()
    {
        return $this->id;
    }
}
