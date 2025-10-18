<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
namespace Magento\Integration\Model\ResourceModel\Oauth\Nonce;

/**
 * OAuth nonce resource collection model
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Initialize collection model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Magento\Integration\Model\Oauth\Nonce::class,
            \Magento\Integration\Model\ResourceModel\Oauth\Nonce::class
        );
    }
}
