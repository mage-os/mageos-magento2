<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Checkout\Test\Unit\Helper;

use Magento\Quote\Api\Data\EstimateAddressInterface;
use Magento\Quote\Api\ShippingMethodManagementInterface as ApiShippingMethodManagementInterface;
use Magento\Quote\Model\ShippingMethodManagementInterface as ModelShippingMethodManagementInterface;

/**
 * Simple shipping method management double for unit tests.
 */
class ShippingMethodManagementDouble implements
    ApiShippingMethodManagementInterface,
    ModelShippingMethodManagementInterface
{
    /**
     * @inheritDoc
     */
    public function estimateByAddress($cartId, EstimateAddressInterface $address)
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function estimateByAddressId($cartId, $addressId)
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getList($cartId)
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function set($cartId, $carrierCode, $methodCode)
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function get($cartId)
    {
        return null;
    }
}
