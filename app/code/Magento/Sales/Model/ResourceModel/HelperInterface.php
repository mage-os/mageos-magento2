<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
namespace Magento\Sales\Model\ResourceModel;

use Magento\Sales\Model\ResourceModel\Report\Bestsellers as BestsellersReport;

/**
 * Sales resource helper interface
 */
interface HelperInterface
{
    /**
     * Update rating position
     *
     * @param string $aggregation One of BestsellersReport::AGGREGATION_XXX constants
     * @param array $aggregationAliases
     * @param string $mainTable
     * @param string $aggregationTable
     * @return $this
     */
    public function getBestsellersReportUpdateRatingPos(
        $aggregation,
        $aggregationAliases,
        $mainTable,
        $aggregationTable
    );
}
