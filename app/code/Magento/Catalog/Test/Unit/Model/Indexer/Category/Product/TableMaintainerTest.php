<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */

declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Model\Indexer\Category\Product;

use Magento\Catalog\Model\Indexer\Category\Product\AbstractAction;
use Magento\Catalog\Model\Indexer\Category\Product\TableMaintainer;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table as DdlTable;
use Magento\Framework\Indexer\ScopeResolver\IndexScopeResolver as TableResolver;
use Magento\Framework\Search\Request\Dimension;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TableMaintainerTest extends TestCase
{
    /** @var ResourceConnection|MockObject */
    private $resource;

    /** @var TableResolver|MockObject */
    private $tableResolver;

    /** @var AdapterInterface|MockObject */
    private $adapter;

    /** @var TableMaintainer */
    private $maintainer;

    protected function setUp(): void
    {
        $this->resource = $this->createMock(ResourceConnection::class);
        $this->tableResolver = $this->createMock(TableResolver::class);
        $this->adapter = $this->createMock(AdapterInterface::class);

        $this->resource->method('getConnection')->willReturn($this->adapter);
        $this->resource->method('getTableName')->willReturnCallback(
            static function (string $name) {
                return 'pref_' . $name;
            }
        );

        $this->maintainer = new TableMaintainer($this->resource, $this->tableResolver);
    }

    public function testGetMainTableAndReplicaTableName(): void
    {
        $storeId = 3;
        $resolvedMain = 'catalog_category_product_index_store3';

        $this->tableResolver->expects($this->atLeastOnce())
            ->method('resolve')
            ->with(AbstractAction::MAIN_INDEX_TABLE, $this->callback(function (array $dims): bool {
                return isset($dims[0]) && $dims[0] instanceof Dimension;
            }))
            ->willReturn($resolvedMain);

        $this->assertSame($resolvedMain, $this->maintainer->getMainTable($storeId));
        $this->assertSame($resolvedMain . '_replica', $this->maintainer->getMainReplicaTable($storeId));
    }

    public function testCreateMainTmpTableAndGetMainTmpTable(): void
    {
        $storeId = 7;
        $resolvedMain = 'index_7';
        $tmpName = $resolvedMain . '_tmp';

        $this->tableResolver->expects($this->any())
            ->method('resolve')
            ->willReturn($resolvedMain);

        $this->adapter->expects($this->once())
            ->method('createTemporaryTableLike')
            ->with($tmpName, $resolvedMain, true);

        // First call creates and caches temp table
        $this->maintainer->createMainTmpTable($storeId);
        // Second call should be a no-op (still once)
        $this->maintainer->createMainTmpTable($storeId);

        self::assertSame($tmpName, $this->maintainer->getMainTmpTable($storeId));

        $this->expectException(\Magento\Framework\Exception\NoSuchEntityException::class);
        // Different store id should not have tmp table created
        $this->maintainer->getMainTmpTable($storeId + 1);
    }

    public function testGetSameAdapterConnectionReturnsSameInstance(): void
    {
        $same = $this->maintainer->getSameAdapterConnection();
        $this->assertSame($this->adapter, $same);
    }
}
