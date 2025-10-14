<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

use Magento\Quote\Api\Data\CartExtension;
use Magento\Quote\Model\ShippingAssignment;

/**
 * Concrete CartExtension test helper exposing shipping assignments methods.
 */
class CartExtensionShippingAssignmentsTestHelper extends CartExtension
{
    /**
     * @var ShippingAssignment[]
     */
    private $shippingAssignments = [];

    /**
     * Empty constructor for unit tests.
     */
    public function __construct()
    {
    }

    /**
     * Returns shipping assignments.
     *
     * @return ShippingAssignment[]
     */
    public function getShippingAssignments()
    {
        return $this->shippingAssignments;
    }

    /**
     * Sets shipping assignments.
     *
     * @param ShippingAssignment[] $shippingAssignments
     * @return $this
     */
    public function setShippingAssignments($shippingAssignments)
    {
        $this->shippingAssignments = $shippingAssignments;
        return $this;
    }
}
