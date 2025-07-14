<?php
/**
 * Copyright 2011 Adobe
 * All Rights Reserved.
 */

/**
 * Newsletter problems collection
 */
namespace Magento\Newsletter\Model\ResourceModel\Grid;

class Collection extends \Magento\Newsletter\Model\ResourceModel\Problem\Collection
{
    /**
     * Adds queue info to grid
     *
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     * |\Magento\Newsletter\Model\ResourceModel\Grid\Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addSubscriberInfo()->addQueueInfo();
        return $this;
    }
}
