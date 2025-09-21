<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
namespace Magento\SalesRule\Model\Data;

/**
 * Class CouponMassDeleteResult
 *
 * @codeCoverageIgnore
 */
class CouponMassDeleteResult extends \Magento\Framework\Api\AbstractSimpleObject implements
    \Magento\SalesRule\Api\Data\CouponMassDeleteResultInterface
{
    const FAILED_ITEMS = 'failed_items';
    const MISSING_ITEMS = 'missing_items';

    /**
     * {@inheritdoc}
     */
    public function getFailedItems()
    {
        return $this->_get(self::FAILED_ITEMS);
    }

    /**
     * {@inheritdoc}
     */
    public function setFailedItems(array $items)
    {
        return $this->setData(self::FAILED_ITEMS, $items);
    }

    /**
     * {@inheritdoc}
     */
    public function getMissingItems()
    {
        return $this->_get(self::MISSING_ITEMS);
    }

    /**
     * {@inheritdoc}
     */
    public function setMissingItems(array $items)
    {
        return $this->setData(self::MISSING_ITEMS, $items);
    }
}
