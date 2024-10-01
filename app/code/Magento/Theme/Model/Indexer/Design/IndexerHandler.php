<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Theme\Model\Indexer\Design;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Indexer\IndexStructureInterface;
use Magento\Framework\Indexer\SaveHandler\Batch;
use Magento\Framework\Indexer\SaveHandler\Grid;
use Magento\Framework\Indexer\SaveHandler\IndexerInterface;
use Magento\Framework\Indexer\ScopeResolver\FlatScopeResolver;
use Magento\Framework\Indexer\ScopeResolver\IndexScopeResolver;
use Magento\Framework\Search\Request\Dimension;
use Magento\Framework\Setup\Declaration\Schema\Dto\Factories\Table as DtoFactoriesTable;

class IndexerHandler extends Grid
{
    /**
     * @var FlatScopeResolver
     */
    private $flatScopeResolver;

    /***
     * Old Charset for flat table
     */
    private const OLDCHARSET = 'utf8mb3';

    /***
     * table design_config_grid_flat
     */
    private const DESIGN_CONFIG_GRID_FLAT = "design_config_grid_flat";

    /***
     * @var DtoFactoriesTable
     */
    private $columnConfig;

    /**
     * @param IndexStructureInterface $indexStructure
     * @param ResourceConnection $resource
     * @param Batch $batch
     * @param IndexScopeResolver $indexScopeResolver
     * @param FlatScopeResolver $flatScopeResolver
     * @param array $data
     * @param int $batchSize
     * @param DtoFactoriesTable|null $dtoFactoriesTable
     */
    public function __construct(
        IndexStructureInterface $indexStructure,
        ResourceConnection $resource,
        Batch $batch,
        IndexScopeResolver $indexScopeResolver,
        FlatScopeResolver $flatScopeResolver,
        array $data,
        $batchSize = 100,
        ?DtoFactoriesTable $dtoFactoriesTable = null
    ) {
        parent::__construct(
            $indexStructure,
            $resource,
            $batch,
            $indexScopeResolver,
            $flatScopeResolver,
            $data,
            $batchSize
        );
        $this->flatScopeResolver = $flatScopeResolver;
        $this->columnConfig = $dtoFactoriesTable ?: ObjectManager::getInstance()->get(DtoFactoriesTable::class);
    }

    /**
     * Clean index table by deleting all records unconditionally or create the index table if not exists
     *
     * @param Dimension[] $dimensions
     * @return IndexerInterface
     */
    public function cleanIndex($dimensions)
    {
        $tableName = $this->flatScopeResolver->resolve($this->getIndexName(), $dimensions);

        if ($this->connection->isTableExists($tableName)) {
            $this->connection->delete($tableName);
            // change the charset to utf8mb4
            if ($tableName === self::DESIGN_CONFIG_GRID_FLAT) {
                $getTableSchema = $this->connection->showTableStatus($tableName);
                $collation = $getTableSchema['Collation'] ?? '';
                if (str_contains($collation, self::OLDCHARSET)) {
                    $charset = $this->columnConfig->getDefaultCharset();
                    $collate = $this->columnConfig->getDefaultCollation();
                    $columnEncoding = " CHARACTER SET ".$charset." COLLATE ".$collate;
                    $this->connection->query(
                        sprintf(
                            'ALTER TABLE `%s` MODIFY COLUMN `theme_theme_id` varchar(255) %s %s,
                             DEFAULT CHARSET=%s, DEFAULT COLLATE=%s',
                            $tableName,
                            $columnEncoding,
                            "COMMENT 'Theme_theme_id'",
                            $charset,
                            $collate
                        )
                    );
                }
            }
        } else {
            $this->indexStructure->create($this->getIndexName(), $this->fields, $dimensions);
        }

        return $this;
    }
}
