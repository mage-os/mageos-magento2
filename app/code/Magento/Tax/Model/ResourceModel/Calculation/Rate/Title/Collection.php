<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
namespace Magento\Tax\Model\ResourceModel\Calculation\Rate\Title;

/**
 * Tax Rate Title Collection
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
            \Magento\Tax\Model\Calculation\Rate\Title::class,
            \Magento\Tax\Model\ResourceModel\Calculation\Rate\Title::class
        );
    }

    /**
     * Add rate id filter
     *
     * @param int $rateId
     * @return \Magento\Tax\Model\ResourceModel\Calculation\Rate\Title\Collection
     */
    public function loadByRateId($rateId)
    {
        $this->addFieldToFilter('main_table.tax_calculation_rate_id', $rateId);
        return $this->load();
    }
}
