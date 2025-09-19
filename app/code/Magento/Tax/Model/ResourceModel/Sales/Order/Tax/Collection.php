<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
namespace Magento\Tax\Model\ResourceModel\Sales\Order\Tax;

/**
 * Order Tax Collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Magento\Tax\Model\Sales\Order\Tax::class,
            \Magento\Tax\Model\ResourceModel\Sales\Order\Tax::class
        );
    }

    /**
     * Retrieve order tax collection by order identifier
     *
     * @param \Magento\Framework\DataObject $order
     * @return \Magento\Tax\Model\ResourceModel\Sales\Order\Tax\Collection
     */
    public function loadByOrder($order)
    {
        $orderId = $order->getId();
        $this->getSelect()->where('main_table.order_id = ?', (int)$orderId)->order('process');
        return $this->load();
    }
}
