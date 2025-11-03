<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Session\Test\Unit\Helper;

use Magento\Framework\Session\SessionManager;

class SessionManagerTestHelper extends SessionManager
{
    /**
     * @var mixed
     */
    private $customerGroupIdReturn = null;

    public function __construct()
    {
        // Empty constructor
    }

    /**
     * @param mixed $return
     * @return $this
     */
    public function setCustomerGroupIdReturn($return)
    {
        $this->customerGroupIdReturn = $return;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCustomerGroupId()
    {
        return $this->customerGroupIdReturn;
    }
}

