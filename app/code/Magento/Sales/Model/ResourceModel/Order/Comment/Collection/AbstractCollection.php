<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
namespace Magento\Sales\Model\ResourceModel\Order\Comment\Collection;

/**
 * Flat sales order abstract comments collection, used as parent for: invoice, shipment, creditmemo
 * phpcs:disable Magento2.Classes.AbstractApi
 * @api
 * @since 100.0.2
 */
abstract class AbstractCollection extends \Magento\Sales\Model\ResourceModel\Collection\AbstractCollection
{
    /**
     * Set filter on comments by their parent item
     *
     * @param \Magento\Framework\Model\AbstractModel|int $parent
     * @return $this
     */
    public function setParentFilter($parent)
    {
        if ($parent instanceof \Magento\Framework\Model\AbstractModel) {
            $parent = $parent->getId();
        }
        return $this->addFieldToFilter('parent_id', $parent);
    }

    /**
     * Adds filter to get only 'visible on front' comments
     *
     * @param int $flag
     * @return $this
     */
    public function addVisibleOnFrontFilter($flag = 1)
    {
        return $this->addFieldToFilter('is_visible_on_front', $flag);
    }

    /**
     * Set created_at sort order
     *
     * @param string $direction
     * @return $this
     */
    public function setCreatedAtOrder($direction = 'desc')
    {
        return $this->setOrder('created_at', $direction);
    }
}
