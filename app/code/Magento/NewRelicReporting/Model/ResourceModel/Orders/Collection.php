<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */

namespace Magento\NewRelicReporting\Model\ResourceModel\Orders;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Initialize orders resource collection
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Magento\NewRelicReporting\Model\Orders::class,
            \Magento\NewRelicReporting\Model\ResourceModel\Orders::class
        );
    }
}
