<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
namespace Magento\Tax\Model\ResourceModel\Calculation\Rate;

/**
 * Tax Rate Title Collection
 */
class Title extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('tax_calculation_rate_title', 'tax_calculation_rate_title_id');
    }

    /**
     * Delete title by rate identifier
     *
     * @param int $rateId
     * @return $this
     */
    public function deleteByRateId($rateId)
    {
        $conn = $this->getConnection();
        $where = $conn->quoteInto('tax_calculation_rate_id = ?', (int)$rateId);
        $conn->delete($this->getMainTable(), $where);

        return $this;
    }
}
