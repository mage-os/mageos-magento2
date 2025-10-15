<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

use Magento\Quote\Api\Data\CartExtension;

/**
 * Lightweight helper to provide getShippingAssignments()/setShippingAssignments() for tests.
 */
class CartExtensionGetShippingAssignmentsTestHelper extends CartExtension
{
    /**
     * @var array|null
     */
    private $shippingAssignments = null;

    /**
     * @return array|null
     */
    public function getShippingAssignments()
    {
        return $this->shippingAssignments;
    }

    /**
     * @param array|null $shippingAssignments
     * @return $this
     */
    public function setShippingAssignments($shippingAssignments)
    {
        $this->shippingAssignments = $shippingAssignments;
        return $this;
    }
}
