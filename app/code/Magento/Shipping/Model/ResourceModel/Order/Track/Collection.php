<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
namespace Magento\Shipping\Model\ResourceModel\Order\Track;

/**
 * Flat sales order shipment tracks collection
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Collection extends \Magento\Sales\Model\ResourceModel\Order\Shipment\Track\Collection
{
    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Magento\Shipping\Model\Order\Track::class,
            \Magento\Sales\Model\ResourceModel\Order\Shipment\Track::class
        );
    }
}
