<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogRule\Test\Unit\Model\Indexer;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Indexer\ActiveTableSwitcher;
use Magento\CatalogRule\Model\Indexer\IndexerTableSwapperInterface;
use Magento\CatalogRule\Model\Indexer\RuleProductsSelectBuilder;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\EntityManager\EntityMetadataInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RuleProductsSelectBuilderTest extends TestCase
{
    /** @var ResourceConnection|MockObject */
    private $resource;

    /** @var Config|MockObject */
    private $eavConfig;

    /** @var StoreManagerInterface|MockObject */
    private $storeManager;

    /** @var MetadataPool|MockObject */
    private $metadataPool;

    /** @var ActiveTableSwitcher|MockObject */
    private $activeTableSwitcher;

    /** @var IndexerTableSwapperInterface|MockObject */
    private $tableSwapper;

    /** @var AdapterInterface|MockObject */
    private $connection;

    /** @var Select|MockObject */
    private $select;

    protected function setUp(): void
    {
        $this->resource           = $this->createMock(ResourceConnection::class);
        $this->eavConfig          = $this->createMock(Config::class);
        $this->storeManager       = $this->createMock(StoreManagerInterface::class);
        $this->metadataPool       = $this->createMock(MetadataPool::class);
        $this->activeTableSwitcher = $this->createMock(ActiveTableSwitcher::class);
        $this->tableSwapper       = $this->createMock(IndexerTableSwapperInterface::class);
        $this->connection         = $this->createMock(AdapterInterface::class);
        $this->select             = $this->createMock(Select::class);

        $this->resource->method('getConnection')->willReturn($this->connection);
        $this->resource->method('getTableName')->willReturnCallback(fn($t) => $t);

        $this->connection->method('select')->willReturn($this->select);
        $this->connection->method('query')->willReturn(
            $this->createMock(\Zend_Db_Statement_Interface::class)
        );
        $this->connection->method('getIfNullSql')->willReturnCallback(
            fn($a, $b) => "IFNULL($a,$b)"
        );

        $backendMock = $this->createMock(AbstractBackend::class);
        $backendMock->method('getTable')->willReturn('catalog_product_entity_decimal');

        $priceMock = $this->createMock(AbstractAttribute::class);
        $priceMock->method('getBackend')->willReturn($backendMock);
        $priceMock->method('getId')->willReturn(77);

        $this->eavConfig->method('getAttribute')
            ->with(Product::ENTITY, 'price')
            ->willReturn($priceMock);

        $metadataMock = $this->createMock(EntityMetadataInterface::class);
        $metadataMock->method('getLinkField')->willReturn('entity_id');
        $this->metadataPool->method('getMetadata')->willReturn($metadataMock);

        $websiteMock = $this->createMock(\Magento\Store\Model\Website::class);
        $websiteMock->method('getDefaultGroup')->willReturn(null);
        $this->storeManager->method('getWebsite')->willReturn($websiteMock);

        $this->select->method('from')->willReturnSelf();
        $this->select->method('order')->willReturnSelf();
        $this->select->method('where')->willReturnSelf();
        $this->select->method('join')->willReturnSelf();
        $this->select->method('joinInner')->willReturnSelf();
        $this->select->method('joinLeft')->willReturnSelf();
        $this->select->method('columns')->willReturnSelf();
    }

    public function testBuildSelectJoinsTierPricesForWebsite(): void
    {
        $joinedTables = [];
        $this->select->method('joinLeft')
            ->willReturnCallback(function ($table) use (&$joinedTables) {
                $joinedTables[] = array_key_first((array)$table);
                return $this->select;
            });

        $this->buildModel()->buildSelect(1, []);

        $this->assertContains('price_tier', $joinedTables,
            'buildSelect must LEFT JOIN catalog_product_entity_tier_price for website-specific tier prices');
        $this->assertContains('price_tier0', $joinedTables,
            'buildSelect must LEFT JOIN catalog_product_entity_tier_price for global (website_id=0) tier prices');
    }

    public function testBuildSelectIncludesTierPricesInLeastExpression(): void
    {
        $columnsArg = null;
        $this->select->method('columns')
            ->willReturnCallback(function ($cols) use (&$columnsArg) {
                $columnsArg = $cols;
                return $this->select;
            });

        $this->buildModel()->buildSelect(1, []);

        $this->assertNotNull($columnsArg);
        $this->assertArrayHasKey('default_price', $columnsArg);
        $this->assertStringContainsString('LEAST(', $columnsArg['default_price'],
            'default_price must use LEAST() to ensure the rule price never exceeds the tier price');
        $this->assertStringContainsString('price_tier', $columnsArg['default_price'],
            'LEAST() expression must include tier price values');
    }

    public function testBuildSelectTierJoinIncludesAllGroupsCondition(): void
    {
        $tierJoinConditions = [];
        $this->select->method('joinLeft')
            ->willReturnCallback(function ($table, $condition) use (&$tierJoinConditions) {
                $alias = array_key_first((array)$table);
                if (in_array($alias, ['price_tier', 'price_tier0'])) {
                    $tierJoinConditions[$alias] = $condition;
                }
                return $this->select;
            });

        $this->buildModel()->buildSelect(1, []);

        foreach (['price_tier', 'price_tier0'] as $alias) {
            $this->assertArrayHasKey($alias, $tierJoinConditions);
            $this->assertStringContainsString('all_groups', $tierJoinConditions[$alias],
                "$alias JOIN condition must handle all_groups=1 tier prices so every customer group benefits");
        }
    }

    private function buildModel(): RuleProductsSelectBuilder
    {
        return new RuleProductsSelectBuilder(
            $this->resource,
            $this->eavConfig,
            $this->storeManager,
            $this->metadataPool,
            $this->activeTableSwitcher,
            $this->tableSwapper
        );
    }
}
