<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
namespace Magento\Framework\Flag;

/**
 * Flag Resource model
 */
#[\Magento\Framework\ObjectManager\Attribute\NonLazy]
class FlagResource extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Define main table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('flag', 'flag_id');
    }
}
