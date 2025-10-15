<?php declare(strict_types=1);
/**
 * Copyright 2023 Adobe
 * All Rights Reserved.
 */

namespace Magento\Eav\Model\Mview\ChangelogBatchWalker;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Select;
use Magento\Framework\Mview\View\ChangelogBatchWalker\IdsFetcherInterface;

class IdsFetcher implements IdsFetcherInterface
{
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private ResourceConnection $resourceConnection;

    /**
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     */
    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @inheritdoc
     */
    public function fetch(Select $select): array
    {
        return $this->resourceConnection->getConnection()->fetchAll($select);
    }
}
