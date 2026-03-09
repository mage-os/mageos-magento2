<?php
/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Sales\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;

class PopulatePeriodMonthColumn implements DataPatchInterface, PatchRevertableInterface
{
    /**
     * @var ResourceConnection
     */
    private ResourceConnection $resourceConnection;

    /**
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @inheritDoc
     */
    public function apply()
    {
        $connection = $this->resourceConnection->getConnection();
        $table = $this->resourceConnection->getTableName('sales_bestsellers_aggregated_daily');

        $connection->query(
            sprintf(
                "UPDATE %s
                 SET period_month = DATE_SUB(period, INTERVAL DAYOFMONTH(period)-1 DAY)",
                $table
            )
        );

        return $this;
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function revert()
    {
        $connection = $this->resourceConnection->getConnection();
        $table = $this->resourceConnection->getTableName('sales_bestsellers_aggregated_daily');

        $connection->query(
            sprintf(
                "UPDATE %s
                 SET period_month = ''",
                $table
            )
        );
    }
}
