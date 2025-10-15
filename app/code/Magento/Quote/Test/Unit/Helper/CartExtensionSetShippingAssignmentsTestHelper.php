<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

use Magento\Quote\Api\Data\CartExtension;

class CartExtensionSetShippingAssignmentsTestHelper extends CartExtension
{
    /**
     * @var array|null
     */
    private $shippingAssignments = null;

    /**
     * @param array|null $shippingAssignments
     * @return $this
     */
    public function setShippingAssignments($shippingAssignments)
    {
        $this->shippingAssignments = $shippingAssignments;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getShippingAssignments()
    {
        return $this->shippingAssignments;
    }
}
