<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Helper;

use Magento\Quote\Api\Data\CartItemInterface;

/**
 * Test helper for GroupInterface
 */
class GroupInterfaceTestHelper extends GroupInterface
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * Skip parent constructor
     */
    public function __construct()
    {
        // Skip parent constructor
    }

    /**
     * setCustomerGroupId (custom method for testing)
     *
     * @param mixed $value
     * @return $this
     */
    public function setCustomerGroupId($value)
    {
        $this->data['customerGroupId'] = $value;
        return $this;
    }

    /**
     * setIgnoreOldQty (custom method for testing)
     *
     * @param mixed $value
     * @return $this
     */
    public function setIgnoreOldQty($value)
    {
        $this->data['ignoreOldQty'] = $value;
        return $this;
    }

    /**
     * setIsSuperMode (custom method for testing)
     *
     * @param mixed $value
     * @return $this
     */
    public function setIsSuperMode($value)
    {
        $this->data['isSuperMode'] = $value;
        return $this;
    }
}
